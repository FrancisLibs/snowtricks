<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CreateTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',  TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom....'
                ]
            ])

            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Descritpion...'
                ]             
            ])

            ->add('category', EntityType::class, [
                'class'         => Category::class,
                'label'         => false,
                'choice_label'  => 'name'
            ])   

            ->add('pictures', FileType::class, [
                'label'     => false,
                'mapped'    => false,
            ])

            ->add('video', FileType::class, [
                'label'     => false,
                'mapped'    => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
