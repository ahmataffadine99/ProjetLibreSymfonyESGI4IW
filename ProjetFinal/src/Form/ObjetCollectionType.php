<?php

namespace App\Form;
use App\Entity\Emplacement;
use App\Entity\Categorie;
use App\Entity\ObjetCollection;
use App\Entity\StatutObjet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjetCollectionType extends AbstractType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'objet',
            ])
            ->add('dateAjout', DateTimeType::class, [
                'label' => 'Date d\'ajout',
                'widget' => 'single_text',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (facultatif)',
                'required' => false,
            ])
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
                'placeholder' => 'Sélectionnez une ou  catégorie',
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
                'required' => false, // L'emplacement peut être facultatif
            ])
           
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObjetCollection::class,
        ]);
    }
}