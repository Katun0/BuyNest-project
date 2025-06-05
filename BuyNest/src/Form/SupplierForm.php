<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Supplier;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('company', TextType::class)
            ->add('cpf_cnpj', TextType::class)
            ->add('phone', TextType::class)
            ->add('postal_code', TextType::class)
            ->add('address', TextType::class)
            ->add('address_number', IntegerType::class)
            ->add('active', CheckboxType::class)
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
