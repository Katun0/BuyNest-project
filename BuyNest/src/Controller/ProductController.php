<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\TurboBundle\TurboStreamResponse;
#[IsGranted('ROLE_ADMIN')]
final class ProductController extends AbstractController
{
#[IsGranted('ROLE_ADMIN')]
    #[Route('/product', name: 'app_product')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        ProductRepository $productRepository,
        SluggerInterface $slugger
    ): Response {
        $showInactive = $request->query->getBoolean('show_inactive', false);

        $products = $showInactive 
            ? $productRepository->findAll() 
            : $productRepository->findAllActive();

        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $product->setCreatedAt($now);
            $product->setLastModified($now);

            // Handle photo upload
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Use the slugger to create a safe filename
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where product photos are stored
                try {
                    $photoFile->move(
                        $this->getParameter('product_photos_directory'),
                        $newFilename
                    );

                    // Update the 'photo' property to store the filename
                    $product->setPhoto($newFilename);
                } catch (\Exception $e) {
                    // Handle exception if something happens during file upload
                    $this->addFlash('error', 'Erro ao fazer upload da foto: ' . $e->getMessage());
                }
            }

            $entityManager->persist($product);
            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'append',
                        'target' => 'products-table-body',
                        'content' => $this->renderView('product/_product_row.html.twig', [
                            'product' => $product
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'product-form',
                        'content' => $this->renderView('product/_form.html.twig', [
                            'form' => $this->createForm(ProductForm::class, new Product())->createView(),
                        ])
                    ]
                ]);
            }

            $this->addFlash('success', 'Produto criado com sucesso!');
            return $this->redirectToRoute('app_product');
        }

        // Create edit forms for each product
        $editForms = [];
        foreach ($products as $existingProduct) {
            $editForms[$existingProduct->getId()] = $this->createForm(ProductForm::class, $existingProduct, [
                'action' => $this->generateUrl('app_product_edit', ['id' => $existingProduct->getId()]),
                'method' => 'POST',
            ])->createView(); // Create a view of the form
        }

        return $this->render('product/index.html.twig', [
            'tittle' => 'Cadastro de Produtos',
            'form' => $form->createView(),
            'products' => $products,
            'editForms' => $editForms, // Ensure this is passed properly
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/product/{id}/edit-form', name: 'app_product_edit_form')]
    public function editForm(Product $product): Response
    {
        $form = $this->createForm(ProductForm::class, $product, [
            'action' => $this->generateUrl('app_product_edit', ['id' => $product->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('product/_edit_form.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/product/{id}/edit', name: 'app_product_edit')]
    public function edit(
        Product $product,
        EntityManagerInterface $entityManager,
        Request $request,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setLastModified(new \DateTimeImmutable());

            // Handle photo upload
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Use the slugger to create a safe filename
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where product photos are stored
                try {
                    $photoFile->move(
                        $this->getParameter('product_photos_directory'),
                        $newFilename
                    );

                    // Delete the old photo file if it exists
                    $oldPhoto = $product->getPhoto();
                    if ($oldPhoto) {
                        $oldPhotoPath = $this->getParameter('product_photos_directory') . '/' . $oldPhoto;
                        if (file_exists($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }

                    // Update the 'photo' property to store the filename
                    $product->setPhoto($newFilename);
                } catch (\Exception $e) {
                    // Handle exception if something happens during file upload
                    $this->addFlash('error', 'Erro ao fazer upload da foto: ' . $e->getMessage());
                }
            }

            $entityManager->flush();

            if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
                return new TurboStreamResponse([
                    [
                        'action' => 'replace',
                        'target' => 'product_' . $product->getId(),
                        'content' => $this->renderView('product/_product_row.html.twig', [
                            'product' => $product
                        ])
                    ],
                    [
                        'action' => 'replace',
                        'target' => 'flash-messages',
                        'content' => '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Produto atualizado com sucesso!</div>'
                    ]
                ]);
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Produto atualizado com sucesso!'
                ]);
            }

            $this->addFlash('success', 'Produto atualizado com sucesso!');
            return $this->redirectToRoute('app_product');
        }

        if ($request->headers->get('Accept') === 'text/vnd.turbo-stream.html') {
            return new TurboStreamResponse([
                [
                    'action' => 'replace',
                    'target' => 'product-form-errors',
                    'content' => $this->renderView('product/_form_errors.html.twig', [
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

        return $this->redirectToRoute('app_product');
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/product/validate/{field}', name: 'app_product_validate_field', methods: ['POST'])]
    public function validateField(
        Request $request,
        string $field
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        // Only validate the specific field
        $fieldForm = $form->get($field);
        $errors = $fieldForm->getErrors(true);

        return new TurboStreamResponse([
            [
                'action' => 'replace',
                'target' => 'product_' . $field . '_error',
                'content' => $this->renderView('product/_field_error.html.twig', [
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

#[IsGranted('ROLE_ADMIN')]
    #[Route('/product/toggle-inactive', name: 'app_product_toggle_inactive', methods: ['GET'])]
    public function toggleInactive(
        Request $request,
        ProductRepository $productRepository
    ): Response
    {
        $showInactive = $request->query->getBoolean('show_inactive', false);

        $products = $showInactive
            ? $productRepository->findAll()
            : $productRepository->findAllActive();

        return new TurboStreamResponse([
            [
                'action' => 'replace',
                'target' => 'products-table-body',
                'content' => $this->renderView('product/_products_table.html.twig', [
                    'products' => $products
                ])
            ]
        ]);
    }

}
