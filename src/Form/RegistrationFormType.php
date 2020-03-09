<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username' , TextType::class , [
                "required" => false
            ] )

            // constraints password not annotation Entity User because
            // settings form for User have not
            // password fields and not constraints password
            ->add('plainPassword', PasswordType::class , [
                "required" => false ,
                "constraints" => [
                    new Length( [
                        "min" => 2 ,
                        "max" => 255 ,
                        "minMessage" => "password min size is 2 characters" ,
                        'maxMessage' => "password max size is 42 characters"
                    ] ) ,
                    new NotBlank() ,
                    // new EqualTo( [
                    //     "propertyPath"=>"password" ,
                    //     "message" => "password cant be diff"
                    // ] )
                ]
            ] )
            ->add('password', PasswordType::class , [
                "required" => false ,
                "constraints" => [
                    // new EqualTo( [
                    //     "propertyPath" => "plainPassword" ,
                    //     "message" => "password cant be diff"
                    // ] )
                ]
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
