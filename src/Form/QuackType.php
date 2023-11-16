<?php

namespace App\Form;

use App\Entity\Quack;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $quack = $options['quack'];
        $builder
            ->add('content')
            ->add('img', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'data_class' => null
            ])
//            ->add('tag', ChoiceType::class, [
//                'label' => 'Add some Tags!',
//                'required' => false,
//
//            ])
//            ->add('tags', CollectionType::class, [
//                'entry_type' => EntityType::class,
//                'entry_options' => [
//                    'class' => Tag::class,
//                    'multiple' => true,
//                    'required' => false,
//                ],
//                'allow_add' => true,
//                'by_reference' => false,
//                'label' => 'Tags',
//                'data' => $quack ? $quack->getTags() : null,
//            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'multiple' => true,
                'required' => false,
                'label' => 'quacktype'
            ])
//            ->add('newTag', TextType::class, [
//                'mapped' => false,
//                'required' => false,
//                'label' => 'Add New Tag',
//            ])
//            ->add('addTag', SubmitType::class, [
//                'label' => 'Add Tag',
//            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quack::class,
            'quack' => null,
        ]);
    }
}
