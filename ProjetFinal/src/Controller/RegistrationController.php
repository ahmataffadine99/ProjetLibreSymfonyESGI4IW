<?php
namespace App\Controller;

use App\Entity\Utilisateur; // <--- MODIFIEZ ICI
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur(); // MODIFIEZ ICI
        $form = $this->createForm(RegistrationFormType::class, $utilisateur); // MODIFIEZ ICI
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $utilisateur->setPassword( // MODIFIEZ ICI
                $userPasswordHasher->hashPassword(
                    $utilisateur, // MODIFIEZ ICI
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($utilisateur); // MODIFIEZ ICI
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('homepage'); // Rediriger après l'enregistrement
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}