<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423151653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie_objet_collection (categorie_id INT NOT NULL, objet_collection_id INT NOT NULL, INDEX IDX_1EE0E2D1BCF5E72D (categorie_id), INDEX IDX_1EE0E2D1B1576E53 (objet_collection_id), PRIMARY KEY(categorie_id, objet_collection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE emplacement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE jeu_video (id INT NOT NULL, studio VARCHAR(255) NOT NULL, plateforme VARCHAR(100) NOT NULL, classification VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE livre (id INT NOT NULL, auteur VARCHAR(255) NOT NULL, isbn VARCHAR(20) DEFAULT NULL, nombre_pages INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE objet_collection (id INT AUTO_INCREMENT NOT NULL, proprietaire_id INT DEFAULT NULL, emplacement_id INT DEFAULT NULL, statut_id INT NOT NULL, categorie_id INT NOT NULL, utilisateur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_ajout DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_2F53F74876C50E4A (proprietaire_id), INDEX IDX_2F53F748C4598A51 (emplacement_id), INDEX IDX_2F53F748F6203804 (statut_id), INDEX IDX_2F53F748BCF5E72D (categorie_id), INDEX IDX_2F53F748FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE objet_collection_tag (objet_collection_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_9A8E8830B1576E53 (objet_collection_id), INDEX IDX_9A8E8830BAD26311 (tag_id), PRIMARY KEY(objet_collection_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE proprietaire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE statut_objet (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE vinyle (id INT NOT NULL, artiste VARCHAR(255) NOT NULL, titre_album VARCHAR(255) NOT NULL, genre VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie_objet_collection ADD CONSTRAINT FK_1EE0E2D1BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie_objet_collection ADD CONSTRAINT FK_1EE0E2D1B1576E53 FOREIGN KEY (objet_collection_id) REFERENCES objet_collection (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE jeu_video ADD CONSTRAINT FK_4E22D9D4BF396750 FOREIGN KEY (id) REFERENCES objet_collection (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livre ADD CONSTRAINT FK_AC634F99BF396750 FOREIGN KEY (id) REFERENCES objet_collection (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection ADD CONSTRAINT FK_2F53F74876C50E4A FOREIGN KEY (proprietaire_id) REFERENCES proprietaire (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection ADD CONSTRAINT FK_2F53F748C4598A51 FOREIGN KEY (emplacement_id) REFERENCES emplacement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection ADD CONSTRAINT FK_2F53F748F6203804 FOREIGN KEY (statut_id) REFERENCES statut_objet (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection ADD CONSTRAINT FK_2F53F748BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection ADD CONSTRAINT FK_2F53F748FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection_tag ADD CONSTRAINT FK_9A8E8830B1576E53 FOREIGN KEY (objet_collection_id) REFERENCES objet_collection (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection_tag ADD CONSTRAINT FK_9A8E8830BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vinyle ADD CONSTRAINT FK_8CD238D0BF396750 FOREIGN KEY (id) REFERENCES objet_collection (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie_objet_collection DROP FOREIGN KEY FK_1EE0E2D1BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie_objet_collection DROP FOREIGN KEY FK_1EE0E2D1B1576E53
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE jeu_video DROP FOREIGN KEY FK_4E22D9D4BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livre DROP FOREIGN KEY FK_AC634F99BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection DROP FOREIGN KEY FK_2F53F74876C50E4A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection DROP FOREIGN KEY FK_2F53F748C4598A51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection DROP FOREIGN KEY FK_2F53F748F6203804
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection DROP FOREIGN KEY FK_2F53F748BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection DROP FOREIGN KEY FK_2F53F748FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection_tag DROP FOREIGN KEY FK_9A8E8830B1576E53
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objet_collection_tag DROP FOREIGN KEY FK_9A8E8830BAD26311
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vinyle DROP FOREIGN KEY FK_8CD238D0BF396750
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categorie_objet_collection
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE emplacement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE jeu_video
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE livre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE objet_collection
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE objet_collection_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE proprietaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE statut_objet
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE vinyle
        SQL);
    }
}
