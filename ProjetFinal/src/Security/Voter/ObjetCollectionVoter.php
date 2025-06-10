<?php
// src/Security/Voter/ObjetCollectionVoter.php

namespace App\Security\Voter;

use App\Entity\ObjetCollection; // L'entité sur laquelle on vote
use App\Entity\Utilisateur;      // L'entité de l'utilisateur
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface; // <--- C'EST LA NOUVELLE LIGNE 'USE' !
// use Symfony\Component\Security\Core\Security; // <--- CETTE LIGNE EST MAINTENANT INUTILE, VOUS POUVEZ LA COMMENTER OU LA SUPPRIMER

class ObjetCollectionVoter extends Voter
{
    // Au lieu de $security, nous utilisons $authorizationChecker
    private $authorizationChecker;

    // Le constructeur reçoit maintenant AuthorizationCheckerInterface
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['EDIT', 'DELETE'])
               && $subject instanceof ObjetCollection;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas connecté, il ne peut rien faire.
        if (!$user instanceof Utilisateur) {
            return false;
        }

        /** @var ObjetCollection $objetCollection */
        $objetCollection = $subject;

        // L'ADMIN peut TOUT faire sur n'importe quel objet.
        // Utilisation de $this->authorizationChecker->isGranted()
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Le MODERATEUR peut TOUT faire sur n'importe quel objet (pour le moment).
        // Utilisation de $this->authorizationChecker->isGranted()
        // Si vous voulez que le modérateur NE DOIT PAS modifier un objet qui n'est pas le sien,
        // vous DEVEZ supprimer ce bloc 'if' ou le conditionner :
        if ($this->authorizationChecker->isGranted('ROLE_MODERATEUR')) {
             return true; // Le modérateur peut faire ce qu'il veut sur les objets
        }

        // La logique suivante gère les utilisateurs normaux (ROLE_USER)
        switch ($attribute) {
            case 'EDIT':
            case 'DELETE':
                return $this->isOwner($objetCollection, $user);
        }

        return false;
    }

    private function isOwner(ObjetCollection $objetCollection, Utilisateur $user): bool
    {
        return $objetCollection->getUtilisateur() === $user;
    }
}