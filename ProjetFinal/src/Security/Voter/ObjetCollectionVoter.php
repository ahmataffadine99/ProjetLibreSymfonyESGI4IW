<?php
// src/Security/Voter/ObjetCollectionVoter.php

namespace App\Security\Voter;

use App\Entity\ObjetCollection;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ObjetCollectionVoter extends Voter
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // On ne gère que les attributs 'EDIT' et 'DELETE' pour l'entité ObjetCollection
        return in_array($attribute, ['EDIT', 'DELETE'])
               && $subject instanceof ObjetCollection;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas connecté ou n'est pas une instance d'Utilisateur, il ne peut rien faire.
        if (!$user instanceof Utilisateur) {
            return false;
        }

        /** @var ObjetCollection $objetCollection */
        $objetCollection = $subject;

        // 1. **Priorité 1 : ROLE_ADMIN**
        // Un ADMINISTRATEUR (ROLE_ADMIN) peut TOUT faire sur n'importe quel objet.
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true; // Accès total
        }

        // 2. **Priorité 2 : Propriétaire de l'objet**
        // L'utilisateur est-il le propriétaire de l'objet ?
        // Cette vérification doit venir AVANT les rôles intermédiaires comme MODERATEUR
        // si le propriétaire doit avoir des droits spécifiques (EDIT/DELETE) sur SES objets.
        if ($this->isOwner($objetCollection, $user)) {
            // Le propriétaire peut TOUJOURS éditer et supprimer SON objet.
            return true;
        }

        // 3. **Priorité 3 : ROLE_MODERATEUR**
        // Après avoir vérifié ADMIN et le propriétaire, on gère le MODERATEUR.
        // Un MODERATEUR (ROLE_MODERATEUR) :
        // - Peut MODIFIER (EDIT) n'importe quel objet (même s'il n'en est pas le propriétaire).
        // - NE PEUT PAS SUPPRIMER (DELETE) d'objets, même si la méthode est appelée.
        if ($this->authorizationChecker->isGranted('ROLE_MODERATEUR')) {
            if ($attribute === 'EDIT') {
                return true; // Le modérateur peut modifier n'importe quel objet
            }
            if ($attribute === 'DELETE') {
                return false; // Le modérateur NE PEUT PAS supprimer d'objets
            }
        }

        // Si aucune des conditions ci-dessus n'a donné un résultat, l'accès est refusé par défaut.
        return false;
    }

    // Méthode utilitaire pour vérifier si l'utilisateur est le propriétaire de l'objet
    private function isOwner(ObjetCollection $objetCollection, Utilisateur $user): bool
    {
        // On s'assure que l'objet a un utilisateur associé avant de comparer
        return $objetCollection->getUtilisateur() !== null && $objetCollection->getUtilisateur()->getId() === $user->getId();
    }
}