<?php

namespace App\Controller\Admin;

use App\Entity\JeuVideo;
use App\Entity\Livre;
use App\Entity\Vinyle;
use App\Repository\ObjetCollectionRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use DateTimeImmutable;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'index')]
    public function index(UtilisateurRepository $utilisateurRepository, ObjetCollectionRepository $objetCollectionRepository): Response
    {
        /** @var \App\Entity\Utilisateur|null $user */
        $user = $this->getUser();

        $livresCount = $this->entityManager->getRepository(Livre::class)->count([]);
        $vinylesCount = $this->entityManager->getRepository(Vinyle::class)->count([]);
        $jeuxVideoCount = $this->entityManager->getRepository(JeuVideo::class)->count([]);

        $totalUsersCount = $utilisateurRepository->count([]);

        $dateLastMonth = (new DateTimeImmutable())->modify('-30 days');
        $recentObjectsCount = $objetCollectionRepository->count(['dateAjout' => $dateLastMonth]);

        return $this->render('admin/index.html.twig', [
            'user' => $user,
            'livresCount' => $livresCount,
            'vinylesCount' => $vinylesCount,
            'jeuxVideoCount' => $jeuxVideoCount,
            'totalUsersCount' => $totalUsersCount,
            'recentObjectsCount' => $recentObjectsCount,
        ]);
    }
}