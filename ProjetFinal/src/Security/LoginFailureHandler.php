<?php
// src/Security/LoginFailureHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Invalid credentials.',
            'error' => $exception->getMessageKey()
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}