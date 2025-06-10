<?php
// src/Controller/Api/CollectionApiController.php

namespace App\Controller\Api;

use App\Entity\ObjetCollection;
use App\Entity\Livre;
use App\Entity\Vinyle;
use App\Entity\JeuVideo;
use App\Entity\Utilisateur;
use App\Repository\ObjetCollectionRepository;
use App\Repository\StatutObjetRepository;
use App\Repository\CategorieRepository;
use App\Repository\EmplacementRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CollectionApiController extends AbstractController
{
    private $objetCollectionRepository;
    private $entityManager;
    private $serializer;
    private $statutObjetRepository;
    private $categorieRepository;
    private $emplacementRepository;
    private $tagRepository;

    public function __construct(
        ObjetCollectionRepository $objetCollectionRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        StatutObjetRepository $statutObjetRepository,
        CategorieRepository $categorieRepository,
        EmplacementRepository $emplacementRepository,
        TagRepository $tagRepository
    ) {
        $this->objetCollectionRepository = $objetCollectionRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->statutObjetRepository = $statutObjetRepository;
        $this->categorieRepository = $categorieRepository;
        $this->emplacementRepository = $emplacementRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Liste tous les objets de collection.
     */
    #[Route('/api/collections', name: 'api_collections_list', methods: ['GET'])]
    public function listAllCollections(): JsonResponse
    {
        $objets = $this->objetCollectionRepository->findAllObjectsWithUser();

        $json = $this->serializer->serialize($objets, 'json', ['groups' => 'collection_read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * Affiche les détails d'un objet de collection spécifique.
     */
    #[Route('/api/collections/{id}', name: 'api_collection_details', methods: ['GET'])]
    public function getCollectionDetails(int $id): JsonResponse
    {
        $objet = $this->objetCollectionRepository->find($id);

        if (!$objet) {
            return new JsonResponse(['message' => 'Objet non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $json = $this->serializer->serialize($objet, 'json', ['groups' => 'collection_read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * Ajoute un nouvel objet de collection.
     */
    #[Route('/api/collections', name: 'api_collection_add', methods: ['POST'])]
    public function addCollection(Request $request): JsonResponse
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Authentification requise.'], Response::HTTP_UNAUTHORIZED);
        }

        $jsonContent = $request->getContent();

        try {
            $data = json_decode($jsonContent, true);
            $type = $data['type'] ?? null;
            $objet = null;

            switch (strtolower($type)) {
                case 'livre':
                    $objet = new Livre();
                    break;
                case 'vinyle':
                    $objet = new Vinyle();
                    break;
                case 'jeu-video':
                    $objet = new JeuVideo();
                    break;
                default:
                    return new JsonResponse(['message' => 'Type d\'objet invalide ou manquant.'], Response::HTTP_BAD_REQUEST);
            }

            $this->serializer->deserialize($jsonContent, get_class($objet), 'json', [
                AbstractNormalizer::OBJECT_TO_POPULATE => $objet,
                'groups' => 'collection_write'
            ]);

            if (isset($data['statut']['id'])) {
                $statut = $this->statutObjetRepository->find($data['statut']['id']);
                if ($statut) $objet->setStatut($statut);
            }
            if (isset($data['categorie']['id'])) {
                $categorie = $this->categorieRepository->find($data['categorie']['id']);
                if ($categorie) $objet->setCategorie($categorie);
            }
            if (isset($data['emplacement']['id'])) {
                $emplacement = $this->emplacementRepository->find($data['emplacement']['id']);
                if ($emplacement) $objet->setEmplacement($emplacement);
            }
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($objet->getTags() as $existingTag) {
                    $objet->removeTag($existingTag);
                }
                foreach ($data['tags'] as $tagData) {
                    if (isset($tagData['id'])) {
                        $tag = $this->tagRepository->find($tagData['id']);
                        if ($tag) $objet->addTag($tag);
                    }
                }
            }

            $objet->setUtilisateur($user);
            $objet->setDateAjout(new \DateTimeImmutable());

            $this->entityManager->persist($objet);
            $this->entityManager->flush();

            $jsonResponse = $this->serializer->serialize($objet, 'json', ['groups' => 'collection_read']);

            return new JsonResponse($jsonResponse, Response::HTTP_CREATED, [], true);

        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de l\'ajout de l\'objet: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Met à jour un objet de collection existant.
     */
    #[Route('/api/collections/{id}', name: 'api_collection_update', methods: ['PUT'])]
    public function updateCollection(Request $request, int $id): JsonResponse
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Authentification requise.'], Response::HTTP_UNAUTHORIZED);
        }

        $objet = $this->objetCollectionRepository->find($id);

        if (!$objet) {
            return new JsonResponse(['message' => 'Objet non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérification de l'autorisation : seul l'utilisateur qui a ajouté l'objet peut le modifier
        // Ou un ADMIN/MODERATEUR selon votre Voter si vous voulez l'implémenter ici
        if ($objet->getUtilisateur() !== $user && !$this->isGranted('ROLE_ADMIN')) { // Ajout de vérification pour ROLE_ADMIN
            return new JsonResponse(['message' => 'Accès refusé. Vous n\'êtes pas l\'auteur de cet objet ou un administrateur.'], Response::HTTP_FORBIDDEN);
        }
        // Note: Le Voter ObjectCollectionVoter gère aussi cela, mais cette vérification ici est une couche supplémentaire pour l'API.

        $jsonContent = $request->getContent();

        try {
            $data = json_decode($jsonContent, true);

            $this->serializer->deserialize($jsonContent, get_class($objet), 'json', [
                AbstractNormalizer::OBJECT_TO_POPULATE => $objet,
                'groups' => 'collection_write'
            ]);

            if (isset($data['statut']['id'])) {
                $statut = $this->statutObjetRepository->find($data['statut']['id']);
                if ($statut) $objet->setStatut($statut);
            } else {
                $objet->setStatut(null);
            }

            if (isset($data['categorie']['id'])) {
                $categorie = $this->categorieRepository->find($data['categorie']['id']);
                if ($categorie) $objet->setCategorie($categorie);
            } else {
                $objet->setCategorie(null);
            }

            if (isset($data['emplacement']['id'])) {
                $emplacement = $this->emplacementRepository->find($data['emplacement']['id']);
                if ($emplacement) $objet->setEmplacement($emplacement);
            } else {
                $objet->setEmplacement(null);
            }

            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($objet->getTags() as $existingTag) {
                    $objet->removeTag($existingTag);
                }
                foreach ($data['tags'] as $tagData) {
                    if (isset($tagData['id'])) {
                        $tag = $this->tagRepository->find($tagData['id']);
                        if ($tag && !$objet->getTags()->contains($tag)) {
                            $objet->addTag($tag);
                        }
                    }
                }
            } else {
                foreach ($objet->getTags() as $existingTag) {
                    $objet->removeTag($existingTag);
                }
            }
            
            $this->entityManager->flush();

            $jsonResponse = $this->serializer->serialize($objet, 'json', ['groups' => 'collection_read']);

            return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);

        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la mise à jour de l\'objet: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    /**
     * Supprime un objet de collection.
     */
    #[Route('/api/collections/{id}', name: 'api_collection_delete', methods: ['DELETE'])]
    public function deleteCollection(int $id): JsonResponse
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Authentification requise.'], Response::HTTP_UNAUTHORIZED);
        }

        $objet = $this->objetCollectionRepository->find($id);

        if (!$objet) {
            return new JsonResponse(['message' => 'Objet non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérification de l'autorisation : seul l'utilisateur qui a ajouté l'objet ou un ADMIN peut le supprimer.
        // Le Voter gère déjà cela, mais c'est une sécurité supplémentaire au niveau de l'API.
        if ($objet->getUtilisateur() !== $user && !$this->isGranted('ROLE_ADMIN')) { // Ajout de vérification pour ROLE_ADMIN
            return new JsonResponse(['message' => 'Accès refusé. Vous n\'êtes pas l\'auteur de cet objet ou un administrateur.'], Response::HTTP_FORBIDDEN);
        }
        
        try {
            $this->entityManager->remove($objet);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression de l\'objet: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Réponse avec un statut 204 No Content (pas de corps de réponse pour une suppression réussie)
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}