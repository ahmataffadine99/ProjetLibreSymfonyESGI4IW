<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface; 
use Symfony\Component\EventDispatcher\EventDispatcherInterface; 
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent; 

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage, // <-- INJECTE TokenStorageInterface
        EventDispatcherInterface $eventDispatcher // <-- INJECTE EventDispatcherInterface
    ): Response {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($utilisateur);
            $entityManager->flush();

           
            $token = new UsernamePasswordToken($utilisateur, 'main', $utilisateur->getRoles());

            $tokenStorage->setToken($token);

            $event = new InteractiveLoginEvent($request, $token);
            $eventDispatcher->dispatch($event, InteractiveLoginEvent::class);

            
            return $this->redirectToRoute('profile_index'); 
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
