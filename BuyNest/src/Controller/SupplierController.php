<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SupplierController extends AbstractController
{
    #[Route('/supplier', name: 'app_supplier')]
    public function index( EntityManagerInterface $entityManager, Request $request): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierForm::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($supplier);
            $entityManager->flush();
        }

        return $this->render('supplier/index.html.twig', [
            'tittle' => ' Cadastro de Fornecedores',
            'form' => $form->createView()
        ]);
    }
}
