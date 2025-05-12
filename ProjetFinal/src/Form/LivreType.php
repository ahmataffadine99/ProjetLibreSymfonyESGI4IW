<?php

namespace App\Form;
use App\Entity\Emplacement;
use App\Entity\Tag;
use App\Entity\Categorie;
use App\Entity\StatutObjet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreType extends ObjetCollectionType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options); // Hérite des champs de ObjetCollectionType

        $builder
            ->add('statut', EntityType::class, [
                'class' => StatutObjet::class,
                'choice_label' => 'nom',
                'label' => 'Statut',
                'placeholder' => 'Sélectionnez un statut',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionnez une catégorie',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'nom',
                'label' => 'Tags',
                'multiple' => true, // Permet de sélectionner plusieurs tags
                'expanded' => true, // Affiche une liste déroulante (vous pouvez mettre true pour des checkboxes)
                'required' => false,
            ])
            ->add('emplacement', EntityType::class, [
                'class' => Emplacement::class,
                'choice_label' => 'nom',
                'label' => 'Emplacement',
                'placeholder' => 'Sélectionnez un emplacement',
                'required' => false,
            ])
            
            ->add('auteur', TextType::class, [
                'label' => 'Auteur',
            ])
            ->add('isbn', TextType::class, [
                'label' => 'ISBN (facultatif)',
                'required' => false,
            ])
            ->add('nombrePages', IntegerType::class, [
                'label' => 'Nombre de pages (facultatif)',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Livre::class,
        ]);
    }
}