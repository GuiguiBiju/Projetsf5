<?php

namespace App\Form;

use App\Entity\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
//                    new Length([
//                        'min' => 6,
//                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        //'max' => 4096,
//                        'max' => 12,
//                        'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères'
//                    ]),
                    new Regex([
                        "pattern" => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/",
                        "message" => "Le mot de passe doit être composé d'au moins une minuscule, une majusccule, un chiffre, un caractere special -+!*$@%_, et avoir entre 8 et 15 caractères"
                    ])
                ],
                "required" => false
            ])

            ->add("prenom", TextType::class, [
                "label" => "Prénom",
                "required" => false
            ])
            ->add("nom", TextType::class, [
                "label" => "Nom",
                "required" => false
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false, //n'essaiera pas de faire correspondre a un champ de la classe abonne
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les C.G.U.',
                    ]),
                ],
                "label" => "C.G.U."
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}
