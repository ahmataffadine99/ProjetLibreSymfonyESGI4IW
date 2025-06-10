<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\UserRolesFormType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface; 


#[Route('/admin/users', name: 'admin_users_')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs = $utilisateurRepository->findAll();

        return $this->render('admin/users/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(int $id, UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createForm(UserRolesFormType::class, $utilisateur);

        return $this->render('admin/users/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(), // Passe le formulaire au template
        ]);
    }

    #[Route('/{id}/update-roles', name: 'update_roles', methods: ['POST'])]
public function updateRoles(Request $request, int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
{
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    $form = $this->createForm(UserRolesFormType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush(); // Doctrine met à jour l'entité automatiquement car elle est liée au formulaire

        $this->addFlash('success', 'Les rôles de l\'utilisateur ont été mis à jour.');

        return $this->redirectToRoute('admin_users_index');
    }

    return $this->render('admin/users/edit.html.twig', [
        'utilisateur' => $utilisateur,
        'form' => $form->createView(),
    ]);
}


    // Nous ajouterons l'action pour traiter la soumission du formulaire ici
}