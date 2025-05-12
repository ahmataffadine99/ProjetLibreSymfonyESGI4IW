<?php

namespace App\Controller\Admin;

use App\Entity\JeuVideo;
use App\Entity\Livre;
use App\Entity\Vinyle;
use App\Form\JeuVideoType;
use App\Form\LivreType;
use App\Form\VinyleType;
use App\Repository\ObjetCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/objets', name: 'admin_objets_')]
#[IsGranted('ROLE_ADMIN')]
class ObjetCollectionController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(ObjetCollectionRepository $objetCollectionRepository): Response
    {
        $objets = $objetCollectionRepository->findAll();

        return $this->render('admin/objet_collection/index.html.twig', [
            'objets' => $objets,
        ]);
    }

    #[Route('/ajouter/{type}', name: 'add')]
    public function add(string $type, Request $request, EntityManagerInterface $entityManager): Response
    {
        $objet = match ($type) {
            'livre' => new Livre(),
            'vinyle' => new Vinyle(),
            'jeu-video' => new JeuVideo(),
            default => throw $this->createNotFoundException('Type d\'objet invalide'),
        };

        $form = $this->createFormForType($type, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objet->setDateAjout(new \DateTimeImmutable());
            $entityManager->persist($objet);
            $entityManager->flush();

            $this->addFlash('success', ucfirst($type) . ' ajouté avec succès.');

            return $this->redirectToRoute('admin_objets_list');
        }

        return $this->render('admin/objet_collection/add.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    #[Route('/modifier/{id}', name: 'edit')]
    public function edit(int $id, Request $request, ObjetCollectionRepository $objetCollectionRepository, EntityManagerInterface $entityManager): Response
    {
        $objet = $objetCollectionRepository->find($id);

        if (!$objet) {
            throw $this->createNotFoundException('Objet non trouvé.');
        }

        $form = $this->createFormForObject($objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Objet modifié avec succès.');

            return $this->redirectToRoute('admin_objets_list');
        }

        return $this->render('admin/objet_collection/edit.html.twig', [
            'objet' => $objet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $id, ObjetCollectionRepository $objetCollectionRepository, EntityManagerInterface $entityManager): Response
    {
        $objet = $objetCollectionRepository->find($id);

        if (!$objet) {
            throw $this->createNotFoundException('Objet non trouvé.');
        }

        $entityManager->remove($objet);
        $entityManager->flush();

        $this->addFlash('success', 'Objet supprimé avec succès.');

        return $this->redirectToRoute('admin_objets_list');
    }

    private function createFormForType(string $type, object $objet): ?\Symfony\Component\Form\FormInterface
    {
        return match ($type) {
            'livre' => $this->createForm(LivreType::class, $objet),
            'vinyle' => $this->createForm(VinyleType::class, $objet),
            'jeu-video' => $this->createForm(JeuVideoType::class, $objet),
            default => null,
        };
    }

    private function createFormForObject(object $objet): ?\Symfony\Component\Form\FormInterface
    {
        return match (true) {
            $objet instanceof Livre => $this->createForm(LivreType::class, $objet),
            $objet instanceof Vinyle => $this->createForm(VinyleType::class, $objet),
            $objet instanceof JeuVideo => $this->createForm(JeuVideoType::class, $objet),
            default => throw new \Exception('Type d\'objet non géré.'),
        };
    }
}