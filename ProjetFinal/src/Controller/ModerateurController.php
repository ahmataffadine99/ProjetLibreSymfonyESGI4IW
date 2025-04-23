<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/moderateur', name: 'moderateur_')]
class ModerateurController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATEUR');

        return $this->render('moderateur/index.html.twig');
    }

    // Vous pouvez ajouter d'autres actions spécifiques aux modérateurs ici
}