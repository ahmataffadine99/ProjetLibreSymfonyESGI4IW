<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; 
use App\Form\ContactFormType;


class DefaultController extends AbstractController
{
    // Si tu as déjà une page d'accueil, elle pourrait ressembler à ça :
    // #[Route('/', name: 'homepage')]
    // public function index(): Response
    // {
    //     return $this->render('index.html.twig', [
    //         'controller_name' => 'DefaultController',
    //     ]);
    // }

    // --- NOUVELLES MÉTHODES POUR LES PAGES STATIQUES ---

    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
#[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])] 
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); 

            

            $this->addFlash('success', 'Votre message a été envoyé avec succès ! Nous vous recontacterons bientôt.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/contact.html.twig', [
            'contactForm' => $form->createView(), 
        ]);
    }
    #[Route('/confidentialite', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('pages/privacy.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}