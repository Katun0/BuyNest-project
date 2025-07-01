<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierForm;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\TurboBundle\TurboStreamResponse;
#[IsGranted('ROLE_ADMIN')]
final class SupplierController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/supplier', name: 'app_supplier')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        SupplierRepository $supplierRepository
    ): Response {
        // Check if we should show inactive suppliers
        $showInactive = $request->query->has('show_inactive');

        // Get suppliers based on the showInactive parameter
        $suppliers = $showInactive ? $supplierRepository->findAll() : $supplierRepository->findAllActive();

        $supplier = new Supplier();
        $form = $this->createForm(SupplierForm::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $supplier->setLastModified($now);

            // Set new suppliers as active by default if not explicitly set
            if ($supplier->isActive() === null) {
                $supplier->setActive(true);
            }

            $entityManager->persist($supplier);
            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'append',
                        'target' => 'suppliers-table-body',
                        'content' => $this->renderView('supplier/_supplier_row.html.twig', [
                            'supplier' => $supplier
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'supplier-form',
                        'content' => $this->renderView('supplier/_form.html.twig', [
                            'form' => $this->createForm(SupplierForm::class, new Supplier())->createView(),
                        ])
                    ]
                ]);
            }

            $this->addFlash('success', 'Fornecedor criado com sucesso!');
            return $this->redirectToRoute('app_supplier');
        }

        // Create edit forms for each supplier
        $editForms = [];
        foreach ($suppliers as $existingSupplier) {
            $editForms[$existingSupplier->getId()] = $this->createForm(SupplierForm::class, $existingSupplier, [
                'action' => $this->generateUrl('app_supplier_edit', ['id' => $existingSupplier->getId()]),
                'method' => 'POST',
            ])->createView(); // Create a view of the form
        }

        return $this->render('supplier/index.html.twig', [
            'tittle' => 'Cadastro de Fornecedores',
            'form' => $form->createView(),
            'suppliers' => $suppliers,
            'editForms' => $editForms, // Ensure this is passed properly
        ]);
    }


    #[Route('/supplier/{id}/edit-form', name: 'app_supplier_edit_form')]
    public function editForm(Supplier $supplier): Response
    {
        $form = $this->createForm(SupplierForm::class, $supplier, [
            'action' => $this->generateUrl('app_supplier_edit', ['id' => $supplier->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('supplier/_edit_form.html.twig', [
            'form' => $form->createView(),
            'supplier' => $supplier,
        ]);
    }

    #[Route('/supplier/{id}/edit', name: 'app_supplier_edit')]
    public function edit(
        Supplier $supplier,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $form = $this->createForm(SupplierForm::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supplier->setLastModified(new \DateTimeImmutable());
            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'replace',
                        'target' => 'supplier_' . $supplier->getId(),
                        'content' => $this->renderView('supplier/_supplier_row.html.twig', [
                            'supplier' => $supplier
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'flash-messages',
                        'content' => '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Fornecedor atualizado com sucesso!</div>'
                    ]
                ]);
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Fornecedor atualizado com sucesso!'
                ]);
            }

            $this->addFlash('success', 'Fornecedor atualizado com sucesso!');
            return $this->redirectToRoute('app_supplier');
        }

        if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
            return new TurboStreamResponse([
                [
                    'action' => 'replace',
                    'target' => 'supplier-form-errors',
                    'content' => $this->renderView('supplier/_form_errors.html.twig', [
                        'form' => $form
                    ])
                ]
            ]);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $this->getFormErrors($form)
            ], 400);
        }

        return $this->redirectToRoute('app_supplier');
    }

    #[Route('/supplier/validate/{field}', name: 'app_supplier_validate_field', methods: ['POST'])]
    public function validateField(
        Request $request,
        string $field
    ): Response {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierForm::class, $supplier);
        $form->handleRequest($request);

        // Only validate the specific field
        $fieldForm = $form->get($field);
        $errors = $fieldForm->getErrors(true);

        return new TurboStreamResponse([
            [
                'action' => 'replace',
                'target' => 'supplier_' . $field . '_error',
                'content' => $this->renderView('supplier/_field_error.html.twig', [
                    'errors' => $errors
                ])
            ]
        ]);
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    #[Route('/supplier/toggle-inactive', name: 'app_supplier_toggle_inactive')]
    public function toggleInactive(
        Request $request,
        SupplierRepository $supplierRepository
    ): Response {
        // Get the show_inactive parameter
        $showInactive = $request->query->has('show_inactive');

        // Get suppliers based on the showInactive parameter
        $suppliers = $showInactive ? $supplierRepository->findAll() : $supplierRepository->findAllActive();

        // Return a Turbo Stream response to update the table
        return new TurboStreamResponse([
            [
                'action' => 'replace',
                'target' => 'suppliers-table-body',
                'content' => $this->renderView('supplier/_suppliers_table.html.twig', [
                    'suppliers' => $suppliers
                ])
            ]
        ]);
    }
}
