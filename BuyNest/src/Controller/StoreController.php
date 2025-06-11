<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\StoreForm;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\TurboBundle\TurboStreamResponse;

final class StoreController extends AbstractController
{
    #[Route('/store', name: 'app_store')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        StoreRepository $storeRepository
    ): Response {
        $showInactive = $request->query->getBoolean('show_inactive', false);

        $stores = $showInactive 
            ? $storeRepository->findAllWithInactive() 
            : $storeRepository->findAllActive();

        $store = new Store();
        $form = $this->createForm(StoreForm::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $store->setLastModified($now);

            $entityManager->persist($store);
            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'append',
                        'target' => 'stores-table-body',
                        'content' => $this->renderView('store/_store_row.html.twig', [
                            'store' => $store
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'store-form',
                        'content' => $this->renderView('store/_form.html.twig', [
                            'form' => $this->createForm(StoreForm::class, new Store())->createView(),
                        ])
                    ]
                ]);
            }

            $this->addFlash('success', 'Loja criada com sucesso!');
            return $this->redirectToRoute('app_store');
        }

        // Create edit forms for each store
        $editForms = [];
        foreach ($stores as $existingStore) {
            $editForms[$existingStore->getId()] = $this->createForm(StoreForm::class, $existingStore, [
                'action' => $this->generateUrl('app_store_edit', ['id' => $existingStore->getId()]),
                'method' => 'POST',
            ])->createView(); // Create a view of the form
        }

        return $this->render('store/index.html.twig', [
            'tittle' => 'Cadastro de Lojas',
            'form' => $form->createView(),
            'stores' => $stores,
            'editForms' => $editForms, // Ensure this is passed properly
        ]);
    }


    #[Route('/store/{id}/edit-form', name: 'app_store_edit_form')]
    public function editForm(Store $store): Response
    {
        $form = $this->createForm(StoreForm::class, $store, [
            'action' => $this->generateUrl('app_store_edit', ['id' => $store->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('store/_edit_form.html.twig', [
            'form' => $form->createView(),
            'store' => $store,
        ]);
    }

    #[Route('/store/{id}/edit', name: 'app_store_edit')]
    public function edit(
        Store $store,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $form = $this->createForm(StoreForm::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $store->setLastModified(new \DateTimeImmutable());
            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'replace',
                        'target' => 'store_' . $store->getId(),
                        'content' => $this->renderView('store/_store_row.html.twig', [
                            'store' => $store
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'flash-messages',
                        'content' => '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Loja atualizada com sucesso!</div>'
                    ]
                ]);
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Loja atualizada com sucesso!'
                ]);
            }

            $this->addFlash('success', 'Loja atualizada com sucesso!');
            return $this->redirectToRoute('app_store');
        }

        if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
            return new TurboStreamResponse([
                [
                    'action' => 'replace',
                    'target' => 'store-form-errors',
                    'content' => $this->renderView('store/_form_errors.html.twig', [
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

        return $this->redirectToRoute('app_store');
    }

    #[Route('/store/validate/{field}', name: 'app_store_validate_field', methods: ['POST'])]
    public function validateField(
        Request $request,
        string $field
    ): Response {
        $store = new Store();
        $form = $this->createForm(StoreForm::class, $store);
        $form->handleRequest($request);

        // Only validate the specific field
        $fieldForm = $form->get($field);
        $errors = $fieldForm->getErrors(true);

        return new TurboStreamResponse([
            [
                'action' => 'replace',
                'target' => 'store_' . $field . '_error',
                'content' => $this->renderView('store/_field_error.html.twig', [
                    'errors' => $errors
                ])
            ]
        ]);
    }
    
    #[Route('/store/toggle-inactive', name: 'app_store_toggle_inactive', methods: ['GET'])]
    public function toggleInactive(
        Request $request,
        StoreRepository $storeRepository
    ): Response {
        $showInactive = $request->query->getBoolean('show_inactive', false);

        $stores = $showInactive
            ? $storeRepository->findAllWithInactive()
            : $storeRepository->findAllActive();

        return new TurboStreamResponse([
            [
                'action' => 'replace',
                'target' => 'stores-table-body',
                'content' => $this->renderView('store/_stores_table.html.twig', [
                    'stores' => $stores
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