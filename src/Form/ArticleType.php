<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('title' , TextType::class )
            ->add('content' , TextareaType::class )
            ->add('isPublic' , CheckboxType::class , [
                "required" => false
            ] )
            ->add('isWarningPublic' , CheckboxType::class , [
                "required" => false
            ] )
            ->add('background' , FileType::class , [
                'mapped' => false ,
                'required' => false ,
                'constraints' => [
                    new File([
                        'maxSize' => '42M',
                        'mimeTypes' => [
                            "image/png" ,
                            "image/svg+xml" ,
                            "image/jpeg"
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
            'data_class' => Article::class,
        ]);
    }
}
