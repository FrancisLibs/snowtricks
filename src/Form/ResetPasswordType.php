<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'attr'  => ['class' => 'reset-form-input'],
                'label_attr'  => ['class' =>  'reset-form-labels']
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type'  =>  PasswordType::class,
                'required'  =>  true,
                'invalid_message'   =>  'Les deux mots de passe doivent être identiques',

                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => ['class'   => 'reset-form-input'],
                    'label_attr'  => ['class' =>  'reset-form-labels']
                ],

                'second_options' => [
                    'label' => 'Vérification',
                    'attr' => array('class'   => 'reset-form-input'),
                    'label_attr'  => ['class' =>  'reset-form-labels']
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
