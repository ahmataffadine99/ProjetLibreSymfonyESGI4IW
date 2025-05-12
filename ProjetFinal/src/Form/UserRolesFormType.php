<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRolesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Modérateur' => 'ROLE_MODERATEUR',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true, // Affiche les options sous forme de checkboxes
                'multiple' => true, // Permet de sélectionner plusieurs rôles
                'label' => 'Rôles',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'validation_groups' => ['admin_edit_roles'], // Groupe de validation spécifique
        ]);
    }
}