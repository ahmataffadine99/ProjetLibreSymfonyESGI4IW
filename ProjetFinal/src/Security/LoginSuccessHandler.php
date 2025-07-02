<?php
// src/Security/LoginSuccessHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        // Si l'utilisateur est authentifié, Symfony gérera le cookie de session automatiquement.
        // On renvoie juste un message de succès.
        return new JsonResponse([
            'message' => 'Login successful!',
            'user' => $user->getUserIdentifier(),
            'redirect_url' => $this->urlGenerator->generate('api_collections_list') // Exemple de redirection API
        ], JsonResponse::HTTP_OK);
    }
}