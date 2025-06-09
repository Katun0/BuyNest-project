<?php

namespace App\Component;

use App\Form\ProductForm;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use App\Entity\Product;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent('product_form')]
class ProductFormComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public Product $product;

    #[LiveProp]
    public bool $isSubmitted = false;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ProductForm::class, $this->product);
    }

    #[LiveAction]
    public function save()
    {
        $form = $this->instantiateForm();
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            // Salva o produto no banco de dados
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->product);
            $entityManager->flush();

            $this->isSubmitted = true;
        }
    }
}