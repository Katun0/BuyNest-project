<?php

namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\Product;
use App\Repository\InventoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController{
    #[Route('/home', name: 'app_home')]
    public function home(
        ProductRepository $productRepository,
        InventoryRepository $inventoryRepository
    ): Response
    {
        $products = $productRepository->findAllActive();

        $quantities = $inventoryRepository->getQuantitiesByProduct();

        return $this->render('home/home.html.twig', [
            'products' => $products,
            'quantities' => $quantities
        ]);
    }
    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, InventoryRepository $inventoryRepository): Response
    {
        $inventories = $inventoryRepository->findByProduct(['product' => $product]);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'inventories' => $inventories,
        ]);
    }
}
