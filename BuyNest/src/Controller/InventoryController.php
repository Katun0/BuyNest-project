<?php

namespace App\Controller;

use App\Entity\Inventory;
use App\Form\InventoryForm;
use App\Repository\InventoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\TurboBundle\TurboStreamResponse;

final class InventoryController extends AbstractController
{
    #[Route('/inventory', name: 'app_inventory')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        InventoryRepository $inventoryRepository
    ): Response {
        // Get all inventory items
        $inventoryItems = $inventoryRepository->findAll();

        $inventory = new Inventory();
        $form = $this->createForm(InventoryForm::class, $inventory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $inventory->setLastModified($now);


            $entityManager->persist($inventory);
            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'append',
                        'target' => 'inventory-table-body',
                        'content' => $this->renderView('inventory/_inventory_row.html.twig', [
                            'inventory' => $inventory
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'inventory-form',
                        'content' => $this->renderView('inventory/_form.html.twig', [
                            'form' => $this->createForm(InventoryForm::class, new Inventory())->createView(),
                        ])
                    ]
                ]);
            }

            $this->addFlash('success', 'Item de inventário criado com sucesso!');
            return $this->redirectToRoute('app_inventory');
        }

        // Create edit forms for each inventory item
        $editForms = [];
        foreach ($inventoryItems as $existingInventory) {
            $editForms[$existingInventory->getId()] = $this->createForm(InventoryForm::class, $existingInventory, [
                'action' => $this->generateUrl('app_inventory_edit', ['id' => $existingInventory->getId()]),
                'method' => 'POST',
            ])->createView(); // Create a view of the form
        }

        return $this->render('inventory/index.html.twig', [
            'tittle' => 'Gestão de Inventário',
            'form' => $form->createView(),
            'inventoryItems' => $inventoryItems,
            'editForms' => $editForms, // Ensure this is passed properly
        ]);
    }


    #[Route('/inventory/{id}/edit-form', name: 'app_inventory_edit_form')]
    public function editForm(Inventory $inventory): Response
    {
        $form = $this->createForm(InventoryForm::class, $inventory, [
            'action' => $this->generateUrl('app_inventory_edit', ['id' => $inventory->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('inventory/_edit_form.html.twig', [
            'form' => $form->createView(),
            'inventory' => $inventory,
        ]);
    }

    #[Route('/inventory/{id}/edit', name: 'app_inventory_edit')]
    public function edit(
        Inventory $inventory,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $form = $this->createForm(InventoryForm::class, $inventory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventory->setLastModified(new \DateTimeImmutable());
            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'replace',
                        'target' => 'inventory_' . $inventory->getId(),
                        'content' => $this->renderView('inventory/_inventory_row.html.twig', [
                            'inventory' => $inventory
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'flash-messages',
                        'content' => '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Item de inventário atualizado com sucesso!</div>'
                    ]
                ]);
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Item de inventário atualizado com sucesso!'
                ]);
            }

            $this->addFlash('success', 'Item de inventário atualizado com sucesso!');
            return $this->redirectToRoute('app_inventory');
        }

        if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
            return new TurboStreamResponse([
                [
                    'action' => 'replace',
                    'target' => 'inventory-form-errors',
                    'content' => $this->renderView('inventory/_form_errors.html.twig', [
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

        return $this->redirectToRoute('app_inventory');
    }

    #[Route('/inventory/validate/{field}', name: 'app_inventory_validate_field', methods: ['POST'])]
    public function validateField(
        Request $request,
        string $field
    ): Response {
        $inventory = new Inventory();
        $form = $this->createForm(InventoryForm::class, $inventory);
        $form->handleRequest($request);

        // Only validate the specific field
        $fieldForm = $form->get($field);
        $errors = $fieldForm->getErrors(true);

        return new TurboStreamResponse([
            [
                'action' => 'replace',
                'target' => 'inventory_' . $field . '_error',
                'content' => $this->renderView('inventory/_field_error.html.twig', [
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

}
