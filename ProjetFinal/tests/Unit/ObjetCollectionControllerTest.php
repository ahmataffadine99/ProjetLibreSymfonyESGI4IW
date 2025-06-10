<?php

namespace App\Tests\Unit;

use App\Controller\ObjetCollectionController;
use App\Entity\Livre;
use App\Entity\Vinyle;
use App\Entity\JeuVideo;
use App\Repository\ObjetCollectionRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment as TwigEnvironment;

class ObjetCollectionControllerTest extends TestCase
{
    public function testDetailsObjetTrouveLivre(): void
    {
        // 1. Préparer les mocks et les données de test
        $objetRepository = $this->createMock(ObjetCollectionRepository::class);
        $twig = $this->createMock(TwigEnvironment::class);
        $livre = new Livre();
        $livre->setTitre('Le Seigneur des Anneaux');

        // Configurer le mock du repository pour retourner notre livre quand on cherche par ID
        $objetRepository->method('find')->with(123)->willReturn($livre);

        // Configurer le mock de Twig pour s'assurer qu'il est appelé avec les bons paramètres
        $twig->expects($this->once())
            ->method('render')
            ->with('objet_collection/details.html.twig', [
                'objet' => $livre,
                'type' => 'livre',
            ])
            ->willReturn(new Response()); // Simuler une réponse Twig

        // 2. Instancier le contrôleur et appeler la méthode
        $controller = new ObjetCollectionController();
        // Injecter les dépendances (mocks) dans le contrôleur (nécessite une méthode pour cela ou via le constructeur si vous le modifiez)
        $controller->setContainer($this->getControllerContainer($objetRepository, $twig));

        $response = $controller->details(123, $objetRepository);

        // 3. Vérifier le résultat
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDetailsObjetTrouveVinyle(): void
    {
        $objetRepository = $this->createMock(ObjetCollectionRepository::class);
        $twig = $this->createMock(TwigEnvironment::class);
        $vinyle = new Vinyle();
        $vinyle->setArtiste('Queen');

        $objetRepository->method('find')->with(456)->willReturn($vinyle);

        $twig->expects($this->once())
            ->method('render')
            ->with('objet_collection/details.html.twig', [
                'objet' => $vinyle,
                'type' => 'vinyle',
            ])
            ->willReturn(new Response());

        $controller = new ObjetCollectionController();
        $controller->setContainer($this->getControllerContainer($objetRepository, $twig));

        $response = $controller->details(456, $objetRepository);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDetailsObjetNonTrouve(): void
    {
        $objetRepository = $this->createMock(ObjetCollectionRepository::class);
        $twig = $this->createMock(TwigEnvironment::class);

        // Configurer le mock du repository pour retourner null si l'objet n'est pas trouvé
        $objetRepository->method('find')->with(789)->willReturn(null);

        $controller = new ObjetCollectionController();
        $controller->setContainer($this->getControllerContainer($objetRepository, $twig));

        // S'assurer qu'une NotFoundHttpException est lancée
        $this->expectException(NotFoundHttpException::class);

        $controller->details(789, $objetRepository);
    }

    // Helper pour simuler le conteneur de dépendances de Symfony
    private function getControllerContainer(ObjetCollectionRepository $objetRepository, TwigEnvironment $twig): \Symfony\Component\DependencyInjection\ContainerInterface
    {
        $container = $this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class);
        $container->method('get')->willReturnMap([
            [ObjetCollectionRepository::class, \Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $objetRepository],
            [TwigEnvironment::class, \Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $twig],
        ]);
        return $container;
    }
}