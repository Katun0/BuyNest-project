<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController{
#[Route('/home', name: 'app_home')]
public function home(ProductRepository $productRepository): Response
{
    $products = $productRepository->findAllActive();

    return $this->render('home/home.html.twig', [
        'products' => $products
    ]);
}}
