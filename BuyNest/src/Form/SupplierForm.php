<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Supplier;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class SupplierForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('company', TextType::class)
            ->add('cpf_cnpj', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 11,
                        'max' => 14,
                        'minMessage' => 'O CPF/CNPJ deve ter no mínimo {{ limit }} caracteres',
                        'maxMessage' => 'O CPF/CNPJ deve ter no máximo {{ limit }} caracteres',
                    ])
                ]
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 11,
                        'minMessage' => 'O telefone deve ter no mínimo {{ limit }} caracteres',
                        'maxMessage' => 'O telefone deve ter no máximo {{ limit }} caracteres',
                    ])
                ]
            ])
            ->add('postal_code', TextType::class)
            ->add('address', TextType::class)
            ->add('address_number', IntegerType::class)
            ->add('active', CheckboxType::class)
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
