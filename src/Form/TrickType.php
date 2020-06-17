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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
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
                'choice_label'  => 'name'
            ])

            ->add('pictures', FileType::class, [
                'label'     => 'Ajout photo',
                'multiple'  => true,
                'mapped'    => false,
                'required'  => false,
            ] )

            ->add('videos',  TextType::class, [
                'required'  => false,
                'mapped'    => false,
                'attr' => [
                    'placeholder' => 'Vidéo....'
                ]
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'translation_domain' => 'forms',
        ]);
    }
}
