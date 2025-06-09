<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ): Response {

        $products = $productRepository->findAll();

        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $now = new \DateTimeImmutable();
            $product->setCreatedAt($now);
            $product->setLastModified($now);

            $entityManager->persist($product);
            $entityManager->flush();
        }

        return $this->render('product/index.html.twig', [
            'tittle' => 'Cadastro de Produtos',
            'form' => $form->createView(),
            'products' => $products
        ]);
    }
}
