<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierForm;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SupplierController extends AbstractController
{#[Route('/supplier', name: 'app_supplier')]
public function index(
    EntityManagerInterface $entityManager,
    Request $request,
    SupplierRepository $supplierRepository
): Response {
    $suppliers = $supplierRepository->findAll();

    $supplier = new Supplier();
    $form = $this->createForm(SupplierForm::class, $supplier);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $now = new \DateTimeImmutable();
        $supplier->setLastModified($now);

        $entityManager->persist($supplier);
        $entityManager->flush();
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

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Fornecedor atualizado com sucesso!'
                ]);
            }

            $this->addFlash('success', 'Fornecedor atualizado com sucesso!');
            return $this->redirectToRoute('app_supplier');
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $this->getFormErrors($form)
            ], 400);
        }

        return $this->redirectToRoute('app_supplier');
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }


    #[Route('/supplier/{id}/edit-form', name: 'app_supplier_edit_form')]
    public function editForm(
        Supplier $supplier,
        Request $request
    ): Response {
        $form = $this->createForm(SupplierForm::class, $supplier, [
            'action' => $this->generateUrl('app_supplier_edit', ['id' => $supplier->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('supplier/_edit_form.html.twig', [
            'form' => $form->createView(),
            'supplier' => $supplier,
        ]);
    }

}


