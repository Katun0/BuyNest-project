<?php

namespace App\Components;

use App\Entity\Supplier;
use App\Form\SupplierForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TurboBundle\TurboStreamResponse;

#[AsLiveComponent('supplier_form')]
class SupplierFormComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Supplier $supplier = null;

    #[LiveProp]
    public bool $isEdit = false;

    #[LiveProp]
    public bool $isSubmitted = false;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SupplierForm::class, $this->supplier);
    }

    #[LiveAction]
    public function save()
    {
        $form = $this->instantiateForm();
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $this->supplier->setLastModified($now);
            
            $this->entityManager->persist($this->supplier);
            $this->entityManager->flush();

            $this->isSubmitted = true;

            // Return a Turbo Stream response to update the grid
            return new TurboStreamResponse([
                [
                    'action' => $this->isEdit ? 'replace' : 'append',
                    'target' => $this->isEdit ? 'supplier_' . $this->supplier->getId() : 'suppliers-table-body',
                    'content' => $this->renderView('supplier/_supplier_row.html.twig', [
                        'supplier' => $this->supplier
                    ])
                ],
                [
                    'action' => 'replace',
                    'target' => 'supplier-form',
                    'content' => $this->isEdit ? '' : $this->renderView('supplier/_form.html.twig', [
                        'form' => $this->instantiateForm()->createView(),
                        'supplier' => new Supplier()
                    ])
                ]
            ]);
        }

        // If validation fails, return the form with errors
        return $this->render('supplier/_form.html.twig', [
            'form' => $form->createView(),
            'supplier' => $this->supplier
        ]);
    }

    #[LiveAction]
    public function validate(string $field)
    {
        $form = $this->instantiateForm();
        $form->handleRequest($this->getRequest());
        
        // Only validate the specific field
        $fieldForm = $form->get($field);
        $errors = $fieldForm->getErrors(true);
        
        // Return a Turbo Stream response to update the field validation
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
}