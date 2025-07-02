<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\Utilisateur;

class LoginApiController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?Utilisateur $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // L'utilisateur est authentifié, Symfony gère la session et les cookies.
        // Vous n'avez pas besoin de renvoyer un token JWT ici si vous utilisez l'authentification par session/cookie.
        // Le simple fait que la requête soit passée par le firewall et ait trouvé un utilisateur suffit.
        return $this->json([
            'user' => $user->getUserIdentifier(), // ou $user->getEmail()
            'message' => 'Logged in successfully!',
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Cette route n'a pas besoin de logique ici.
        // La déconnexion sera gérée par Symfony via le firewall.
        // Il suffit d'appeler cette route et le cookie de session sera invalidé.
        throw new \Exception('This should not be reached! Your logout firewall should intercept this request.');
    }
}