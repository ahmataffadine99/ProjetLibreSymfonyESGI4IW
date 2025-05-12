<?php

namespace App\Form;

use App\Entity\Utilisateur; // <--- MODIFIEZ ICI
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; 
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre email.',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer une adresse email valide.',
                    ]),
                    new Regex([
                        'pattern' => '/@(gmail\.com|yahoo\.fr|hotmail\.fr)$/',
                        'message' => 'Votre adresse email doit se terminer par @gmail.com, @yahoo.fr ou @hotmail.fr.',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
                'label' => 'J\'accepte les conditions',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => ['autocomplete' => 'new-password']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'mapped' => false, // Ne pas mapper directement à une propriété de l'entité Utilisateur
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class, // MODIFIEZ ICI
        ]);
    }
}