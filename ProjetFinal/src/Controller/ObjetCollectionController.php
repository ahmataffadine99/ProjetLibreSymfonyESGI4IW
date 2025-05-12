<?php

namespace App\Controller;

use App\Repository\ObjetCollectionRepository;
use App\Entity\Livre;
use App\Entity\ObjetCollection;
use App\Entity\Vinyle;
use App\Entity\JeuVideo;
use App\Form\LivreType;
use App\Form\VinyleType;
use App\Form\JeuVideoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface; 


#[IsGranted('ROLE_USER')]
class ObjetCollectionController extends AbstractController
{
    #[Route('/ma-collection', name: 'ma_collection')]
public function maCollection(ObjetCollectionRepository $objetCollectionRepository, Request $request): Response
{
    $typeFiltre = $request->query->get('type');
    $objetsWithType = [];

    if ($typeFiltre) {
        $objets = $objetCollectionRepository->findByType($typeFiltre);
        $objetsWithType = $objets; // $findByType doit retourner des ObjetCollection
    } else {
        $objetsWithType = $objetCollectionRepository->findAll(); // findAll() doit retourner des ObjetCollection
    }

    return $this->render('objet_collection/ma_collection.html.twig', [
        'objetsWithType' => $objetsWithType,
    ]);
}
    #[Route('/details/{id}', name: 'objet_collection_details')]
    public function details(int $id, ObjetCollectionRepository $objetCollectionRepository): Response
    {
        $objet = $objetCollectionRepository->find($id);
    
        if (!$objet) {
            throw $this->createNotFoundException('Objet non trouvé.');
        }
    
        // Ajouter un attribut "type" à l'objet pour Twig
        if ($objet instanceof \App\Entity\Livre) {
            $type = 'livre';
        } elseif ($objet instanceof \App\Entity\Vinyle) {
            $type = 'vinyle';
        } elseif ($objet instanceof \App\Entity\JeuVideo) {
            $type = 'jeu_video';
        } else {
            $type = 'autre';
        }
    
        return $this->render('objet_collection/details.html.twig', [
            'objet' => $objet,
            'type' => $type,
        ]);
    }



    #[Route('/modifier/objet/{id}', name: 'objet_modifier')]
    public function modifier(int $id, ObjetCollectionRepository $objetCollectionRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $objet = $objetCollectionRepository->find($id);
    
        if (!$objet) {
            throw $this->createNotFoundException('Objet non trouvé.');
        }
    
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


    #[Route('/supprimer/objet/{id}', name: 'objet_supprimer')]
public function supprimer(int $id, ObjetCollectionRepository $objetCollectionRepository, EntityManagerInterface $entityManager): Response
{
    $objet = $objetCollectionRepository->find($id);

    if (!$objet) {
        throw $this->createNotFoundException('Objet non trouvé.');
    }

    $entityManager->remove($objet);
    $entityManager->flush();

    $this->addFlash('success', 'L\'objet a été supprimé avec succès.');

    return $this->redirectToRoute('ma_collection');
}
    #[Route('/ajouter/{type}', name: 'objet_ajouter')]
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
            // Associer l'utilisateur courant à l'objet
            $objet->setUtilisateur($this->getUser());
            $objet->setDateAjout(new \DateTimeImmutable()); // Si ce n'est pas déjà géré par le formulaire

            $entityManager->persist($objet);
            $entityManager->flush();

            $this->addFlash('success', 'L\'objet a été ajouté avec succès.');

            return $this->redirectToRoute('homepage'); // Rediriger vers la page d'accueil ou une autre page
        }

        return $this->render('objet_collection/ajouter.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}