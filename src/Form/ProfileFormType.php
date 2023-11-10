<?php

namespace App\Form;

use App\Entity\Duck;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $option): void
    {
        $builder
            ->add('duckname')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
        ;
    }

    public function configurationOption(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Duck::class,
        ]);
    }
}