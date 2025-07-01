<?php

namespace App\Form;

use App\Entity\Inventory;
use App\Entity\Product;
use App\Entity\Store;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class InventoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $entityRepository) {
                 return $entityRepository->createQueryBuilder('p')
                     ->where('p.active = true')
                     ->orderBy('p.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor selecione um produto',
                    ]),
                ],
            ])
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor selecione uma loja',
                    ]),
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'A quantidade não pode estar vazia',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'A quantidade deve ser maior ou igual a zero',
                    ]),
                ],
            ])
            ->add('price', NumberType::class, [
                'scale' => 2,
                'constraints' => [
                    new NotBlank([
                        'message' => 'O preço não pode estar vazio',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'O preço deve ser maior ou igual a zero',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class,
        ]);
    }
}
