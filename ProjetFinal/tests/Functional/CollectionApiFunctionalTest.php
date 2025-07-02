<?php
// tests/Functional/CollectionApiFunctionalTest.php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Utilisateur;
use App\Entity\Livre;
use App\Entity\StatutObjet;
use App\Entity\Categorie;
use App\Entity\Emplacement;
use App\Entity\Tag;
use DateTimeImmutable;

class CollectionApiFunctionalTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $connection; // Ajouter une propriété pour la connexion DBAL

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        // Obtenir la connexion DBAL pour exécuter des requêtes SQL brutes
        $this->connection = $this->entityManager->getConnection(); 

        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanDatabase();
        $this->entityManager->close();
        $this->entityManager = null;
        $this->connection = null; // Nettoyer la connexion
    }

    private function cleanDatabase(): void
    {
        // Désactiver temporairement la vérification des contraintes de clés étrangères (pour les cas complexes)
        // Cela permet de vider les tables sans se soucier de l'ordre exact, puis de les réactiver.
        // C'est une méthode très efficace pour les tests.
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');

        // 1. Vider la table de jointure Manay-to-Many AVANT TOUT
        // On utilise TRUNCATE TABLE pour un nettoyage complet et rapide,
        // et pour réinitialiser les auto-incréments si la table en a.
        $this->connection->executeQuery('TRUNCATE TABLE objet_collection_tag;');
        
        // 2. Supprimer les entités "enfants" spécifiques (héritent d'ObjetCollection)
        $this->entityManager->createQuery('DELETE FROM App\Entity\Livre')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Vinyle')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\JeuVideo')->execute();
        
        // 3. Supprimer l'entité mère (ObjetCollection)
        $this->entityManager->createQuery('DELETE FROM App\Entity\ObjetCollection')->execute();
        
        // 4. Supprimer les autres entités dépendantes ou indépendantes
        $this->entityManager->createQuery('DELETE FROM App\Entity\Tag')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Categorie')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Emplacement')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\StatutObjet')->execute();
        
        // 5. Supprimer les entités Utilisateur (souvent des parents pour d'autres relations)
        $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur')->execute();
        
        // Si vous avez une entité Proprietaire, supprimez-la si elle est liée à Utilisateur ou ObjetCollection
        // $this->entityManager->createQuery('DELETE FROM App\Entity\Proprietaire')->execute();

        // Réactiver la vérification des contraintes de clés étrangères
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
        
        $this->entityManager->flush();
    }

    private function createUser(string $email, string $password, array $roles = ['ROLE_USER']): Utilisateur
    {
        $user = new Utilisateur();
        $user->setEmail($email);
        $user->setPassword(static::getContainer()->get('security.user_password_hasher')->hashPassword($user, $password));
        $user->setRoles($roles);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    private function createDependencies(): array
    {
        $statut = new StatutObjet();
        $statut->setNom('Disponible');
        $this->entityManager->persist($statut);

        $categorie = new Categorie();
        $categorie->setNom('Science-Fiction');
        $this->entityManager->persist($categorie);

        $emplacement = new Emplacement();
        $emplacement->setNom('Bibliothèque');
        $this->entityManager->persist($emplacement);

        $tag1 = new Tag();
        $tag1->setNom('Fantastique');
        $this->entityManager->persist($tag1);

        $tag2 = new Tag();
        $tag2->setNom('Aventure');
        $this->entityManager->persist($tag2);

        $this->entityManager->flush();

        return [
            'statut' => $statut,
            'categorie' => $categorie,
            'emplacement' => $emplacement,
            'tags' => [$tag1, $tag2]
        ];
    }

    public function testGetCollectionDetailsSuccessfully(): void
    {
        // 1. Préparation des données dans la base de données de test
        $user = $this->createUser('testuser@example.com', 'password123');
        $deps = $this->createDependencies();

        $livre = new Livre();
        $livre->setNom('Un livre de test');
        $livre->setDateAjout(new DateTimeImmutable());
        $livre->setAuteur('Auteur Test');
        $livre->setStatut($deps['statut']);
        $livre->setCategorie($deps['categorie']);
        $livre->setEmplacement($deps['emplacement']);
        $livre->setUtilisateur($user);
        foreach ($deps['tags'] as $tag) {
            $livre->addTag($tag);
        }
        $this->entityManager->persist($livre);
        $this->entityManager->flush();

        // 2. Authentification du client
        $this->client->loginUser($user);

        // 3. Exécution de la requête GET
        $this->client->request('GET', '/api/collections/' . $livre->getId());

        // 4. Assertions
        $this->assertResponseIsSuccessful(); // Vérifie que le statut HTTP est 2xx
        $this->assertResponseHeaderSame('Content-Type', 'application/json'); // Vérifie le type de contenu

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertEquals($livre->getId(), $responseData['id']);
        $this->assertEquals('Un livre de test', $responseData['nom']);
        $this->assertEquals('Auteur Test', $responseData['auteur']);
        $this->assertEquals('Disponible', $responseData['statut']['nom']);
        $this->assertEquals('Science-Fiction', $responseData['categorie']['nom']);
        $this->assertEquals('Bibliothèque', $responseData['emplacement']['nom']);
        $this->assertCount(2, $responseData['tags']); // Vérifie que les tags sont présents
        $this->assertEquals('Fantastique', $responseData['tags'][0]['nom']);
    }

    public function testGetCollectionDetailsNotFound(): void
    {
        // Pas besoin d'authentifier si on cherche un objet inexistant
        $this->client->request('GET', '/api/collections/99999'); // Un ID qui n'existe pas

        $this->assertResponseStatusCodeSame(404); // Vérifie que le statut HTTP est 404 (Non trouvé)
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Objet non trouvé', $responseData['message']);
    }

    public function testGetCollectionDetailsRequiresAuthentication(): void
    {
        // Création d'un objet sans authentifier le client dans ce test
        $user = $this->createUser('anotheruser@example.com', 'testpass');
        $deps = $this->createDependencies();

        $livre = new Livre();
        $livre->setNom('Livre non autorisé');
        $livre->setDateAjout(new DateTimeImmutable());
        $livre->setAuteur('Auteur Sécurisé');
        $livre->setStatut($deps['statut']);
        $livre->setCategorie($deps['categorie']);
        $livre->setEmplacement($deps['emplacement']);
        $livre->setUtilisateur($user);
        $this->entityManager->persist($livre);
        $this->entityManager->flush();

        // Tentative d'accès sans être connecté
        $this->client->request('GET', '/api/collections/' . $livre->getId());

        // Puisque security.yaml a `IS_AUTHENTICATED_FULLY` pour `^/api`, on devrait être redirigé ou recevoir un 401
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Authentification requise.', $responseData['message']); // Ou 'Invalid credentials.' selon la config
    }
}