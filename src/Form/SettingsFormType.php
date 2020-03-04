<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email' , EmailType::class , [
                "required" => false
            ] )
            ->add('fname' , TextType::class , [
                "required" => false
            ] )
            ->add('name' , TextType::class , [
                "required" => false
            ] )
            ->add('isPublicEmail' ,  CheckboxType::class , [
                "required" => false ,
                "label" => "public email"
            ]  )
            ->add('isPublicProfil' , CheckboxType::class , [
                "required" => false ,
                "label" => "public profil"
            ] )
            ->add('avatar' , FileType::class , [
                'mapped' => false ,
                'required' => false ,
                'constraints' => [
                    new File([
                        'maxSize' => '35M',
                        'mimeTypes' => [
                            "image/png" ,
                            "image/svg+xml" ,
                            "image/jpeg" ,
                            "image/gif"
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
