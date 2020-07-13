<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'required'  =>  true,
                'label'     =>  'Name',
                'attr'      =>  [
                    'placeholder' => 'Name...'
                ]
            ])

            ->add('description', TextareaType::class, [
                'required'  =>  false,
                'label'     =>  'Description',
                'attr'      =>  [
                    'placeholder' => 'Description...'
                ]
            ])

            ->add('category', EntityType::class, [
                'choice_label'  => 'name',
                'label'         => 'Category',
                'class'         => Category::class,
                
            ])

            ->add('pictureFiles', FileType::class, [
                'label'     => 'Pictures',
                'required'  =>  false,
                'multiple'  =>  true,
            ])

            ->add('videoFile',  TextType::class, [
                'required'  =>  false,
                'label'     =>  'Video',
                'attr'      =>  [
                    'placeholder' => 'Video /embed/ link...'
                ]
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
