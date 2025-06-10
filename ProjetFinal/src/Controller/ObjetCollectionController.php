<?php

namespace App\Controller;

use App\Repository\ObjetCollectionRepository;
use App\Entity\Livre;
use App\Entity\ObjetCollection;
use App\Entity\Vinyle;
use App\Entity\JeuVideo;
use App\Entity\Utilisateur; // N'oubliez pas d'importer Utilisateur
use App\Form\LivreType;
use App\Form\VinyleType;
use App\Form\JeuVideoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; // Assurez-vous que c'est bien importé

#[IsGranted('ROLE_USER')]
class ObjetCollectionController extends AbstractController
{
    #[Route('/ma-collection', name: 'ma_collection')]
    public function maCollection(ObjetCollectionRepository $objetCollectionRepository, Request $request): Response
    {
        /** @var Utilisateur $user */ 
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir votre collection.');
            return $this->redirectToRoute('app_login'); 
        }

        $typeFiltre = $request->query->get('type');
        $rawObjets = []; // Renommé pour plus de clarté: ce sont les objets Doctrine bruts

        if ($typeFiltre) {
            $rawObjets = $objetCollectionRepository->findByUserAndType($user, $typeFiltre);
        } else {
            $rawObjets = $objetCollectionRepository->findByUser($user);
        }

        // --- NOUVEAU CODE : Préparer les objets pour le template avec le type en string ---
        $objetsForTemplate = [];
        foreach ($rawObjets as $item) {
            $typeString = 'Type inconnu'; // Valeur par défaut

            if ($item instanceof Livre) {
                $typeString = 'Livre';
            } elseif ($item instanceof Vinyle) {
                $typeString = 'Vinyle';
            } elseif ($item instanceof JeuVideo) {
                $typeString = 'Jeu Vidéo';
            }

            // Nous créons un tableau associatif pour chaque objet
            // Cela inclut toutes les propriétés nécessaires et le type sous forme de string
            $objetsForTemplate[] = [
                'id' => $item->getId(),
                'nom' => $item->getNom(),
                'type' => $typeString, // Le type déterminé en PHP pur (chaîne de caractères)
                'dateAjout' => $item->getDateAjout(),
                'statut' => $item->getStatut(), 
                'categorie' => $item->getCategorie(),
                'tags' => $item->getTags(),
                'emplacement' => $item->getEmplacement(),
                // Laissez l'objet original si vos Voters en ont besoin.
                // Sinon, si les Voters sont basés sur l'ID, vous pouvez l'enlever pour optimiser.
                'originalObject' => $item 
            ];
        }
        // --- FIN NOUVEAU CODE ---

        return $this->render('objet_collection/ma_collection.html.twig', [
            'objetsWithType' => $objetsForTemplate, // Passe le tableau préparé
        ]);
    }

    // --- Le reste de votre contrôleur inchangé (details, modifier, supprimer, ajouter) ---
    // Je ne les répète pas ici pour ne pas alourdir la réponse.
    // Assurez-vous que les méthodes comme details, modifier, supprimer sont bien à jour
    // et qu'elles utilisent le bon typage d'argument (e.g., ObjetCollection $objet)
    // et les annotations #[IsGranted] si vous les utilisez pour la sécurité.

    #[Route('/details/{id}', name: 'objet_collection_details')]
    public function details(int $id, ObjetCollectionRepository $objetCollectionRepository): Response
    {
        $objet = $objetCollectionRepository->find($id);
    
        if (!$objet) {
            throw $this->createNotFoundException('Objet non trouvé.');
        }
    
        // Cette partie détermine le type pour la vue "détails" (qui est une vue séparée)
        $type = 'autre';
        if ($objet instanceof Livre) {
            $type = 'livre';
        } elseif ($objet instanceof Vinyle) {
            $type = 'vinyle';
        } elseif ($objet instanceof JeuVideo) {
            $type = 'jeu_video';
        } 
    
        return $this->render('objet_collection/details.html.twig', [
            'objet' => $objet,
            'type' => $type,
        ]);
    }

    #[Route('/modifier/objet/{id}', name: 'objet_modifier')]
    #[IsGranted('EDIT', subject: 'objet')] // Si vous utilisez les Voters
    public function modifier(ObjetCollection $objet, ObjetCollectionRepository $objetCollectionRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        // La méthode createFormForObject attend un objet et non un ID
        $form = $this->createFormForObject($objet); 
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            $this->addFlash('success', 'L\'objet a été modifié avec succès.');
    
            return $this->redirectToRoute('objet_collection_details', ['id' => $objet->getId()]);
        }
    
        return $this->render('objet_collection/modifier.html.twig', [
            'objet' => $objet,
            'form' => $form->createView(),
        ]);
    }
    
    private function createFormForObject(ObjetCollection $objet): FormInterface
    {
        if ($objet instanceof Livre) {
            return $this->createForm(LivreType::class, $objet);
        } elseif ($objet instanceof Vinyle) {
            return $this->createForm(VinyleType::class, $objet);
        } elseif ($objet instanceof JeuVideo) {
            return $this->createForm(JeuVideoType::class, $objet);
        } else {
            throw new \Exception('Type d\'objet non géré pour la modification.');
        }
    }


    #[Route('/supprimer/objet/{id}', name: 'objet_supprimer', methods: ['POST'])] // Ajout de la méthode POST recommandée
    #[IsGranted('DELETE', subject: 'objet')] // Si vous utilisez les Voters
    public function supprimer(ObjetCollection $objet, ObjetCollectionRepository $objetCollectionRepository, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($objet);
        $entityManager->flush();

        $this->addFlash('success', 'L\'objet a été supprimé avec succès.');

        return $this->redirectToRoute('ma_collection');
    }

    #[Route('/ajouter/{type}', name: 'objet_ajouter')]
    #[IsGranted('ROLE_USER')]
    public function ajouter(string $type, Request $request, EntityManagerInterface $entityManager): Response
    {
        $objet = match ($type) {
            'livre' => new Livre(),
            'vinyle' => new Vinyle(),
            'jeu-video' => new JeuVideo(),
            default => throw $this->createNotFoundException('Type d\'objet invalide'),
        };

        $form = match ($type) {
            'livre' => $this->createForm(LivreType::class, $objet),
            'vinyle' => $this->createForm(VinyleType::class, $objet),
            'jeu-video' => $this->createForm(JeuVideoType::class, $objet),
            default => null,
        };

        if ($form === null) {
            throw $this->createNotFoundException('Type d\'objet invalide');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Utilisateur $user */ 
            $user = $this->getUser();
            if ($user) {
                 $objet->setUtilisateur($user);
            }
            
            $objet->setDateAjout(new \DateTimeImmutable()); 

            $entityManager->persist($objet);
            $entityManager->flush();

            $this->addFlash('success', 'L\'objet a été ajouté avec succès.');

            return $this->redirectToRoute('ma_collection');
        }

        return $this->render('objet_collection/ajouter.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}