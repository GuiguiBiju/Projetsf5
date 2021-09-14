<?php

namespace App\Form;

use App\Entity\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $abonne = $options["data"]; //la variable entité utilisé pour créer le formulaire se trouve dans $option["data"]
        $builder
            ->add('pseudo')
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "lecteur" => "ROLE_LECTEUR",
                    "Bibliothècaire" => "ROLE_BIBLIO",
                    "Directeur" => "ROLE_ADMIN",
                    "Abonné" => "ROLE_USER",
                    "Développeur" => "ROLE_DEV"
                ],
                "multiple" => true,
                "expanded" => true,
                "label" => "Autorisations"
            ])
            ->add('password', TextType::class, [
                "required" => $abonne->getId() ? false : true, // si l'id n'est pas vide alors le password n'est pas requis, sinon, c'est qu'il s'agit d'un ajout, et donc il est requis
                "mapped" => false // mapped = false permet de ne pas lier l'input password à la propriété de l'objet
            ])
            ->add('nom')
            ->add('prenom')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}
