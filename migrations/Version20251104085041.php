<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104085041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achat (id INT AUTO_INCREMENT NOT NULL, fournisseur_id INT NOT NULL, date_achat DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', montant_total NUMERIC(10, 2) DEFAULT NULL, etat VARCHAR(50) DEFAULT NULL, date_creation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_26A98456670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, date_creation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, date_creation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_achat (id INT AUTO_INCREMENT NOT NULL, fournisseur_id INT NOT NULL, reference VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, INDEX IDX_1FC15B95670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE detail_achat (id INT AUTO_INCREMENT NOT NULL, achat_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) DEFAULT NULL, sous_total NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_5B594F0FFE95D117 (achat_id), INDEX IDX_5B594F0FF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE detail_vente (id INT AUTO_INCREMENT NOT NULL, vente_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) DEFAULT NULL, sous_total NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_F57AE1157DC7170A (vente_id), INDEX IDX_F57AE115F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, telephone VARCHAR(50) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, num_tva VARCHAR(50) DEFAULT NULL, date_derniere_commande DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_creation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne_commande (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, produit_id INT DEFAULT NULL, quantite INT DEFAULT NULL, INDEX IDX_3170B74B82EA2E54 (commande_id), INDEX IDX_3170B74BF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mouvement_stock (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, achat_id INT DEFAULT NULL, vente_id INT DEFAULT NULL, quantite INT NOT NULL, type_mouvement VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_61E2C8EBF347EFB (produit_id), INDEX IDX_61E2C8EBFE95D117 (achat_id), INDEX IDX_61E2C8EB7DC7170A (vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, categorie_produit_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, prix_unitaire NUMERIC(12, 2) NOT NULL, quantite_stock INT NOT NULL, reference VARCHAR(64) NOT NULL, date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', tva NUMERIC(5, 2) DEFAULT NULL, stock_min INT DEFAULT NULL, en_commande INT DEFAULT 0 NOT NULL, INDEX IDX_29A5EC2791FDB457 (categorie_produit_id), UNIQUE INDEX uniq_produit_reference (reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit_fournisseur (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, fournisseur_id INT NOT NULL, prix DOUBLE PRECISION DEFAULT NULL, delai_livraison INT DEFAULT NULL, date_creation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_48868EB6F347EFB (produit_id), INDEX IDX_48868EB6670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, date_naissace DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente (id INT AUTO_INCREMENT NOT NULL, vente_termine_id INT DEFAULT NULL, client_id INT NOT NULL, date_vente DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', montant_total NUMERIC(12, 2) DEFAULT NULL, etat VARCHAR(50) DEFAULT NULL, date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_modification DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_888A2A4C60A42ED (vente_termine_id), INDEX IDX_888A2A4C19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE achat ADD CONSTRAINT FK_26A98456670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE commande_achat ADD CONSTRAINT FK_1FC15B95670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE detail_achat ADD CONSTRAINT FK_5B594F0FFE95D117 FOREIGN KEY (achat_id) REFERENCES achat (id)');
        $this->addSql('ALTER TABLE detail_achat ADD CONSTRAINT FK_5B594F0FF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE detail_vente ADD CONSTRAINT FK_F57AE1157DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('ALTER TABLE detail_vente ADD CONSTRAINT FK_F57AE115F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande_achat (id)');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74BF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EBF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EBFE95D117 FOREIGN KEY (achat_id) REFERENCES achat (id)');
        $this->addSql('ALTER TABLE mouvement_stock ADD CONSTRAINT FK_61E2C8EB7DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2791FDB457 FOREIGN KEY (categorie_produit_id) REFERENCES categorie_produit (id)');
        $this->addSql('ALTER TABLE produit_fournisseur ADD CONSTRAINT FK_48868EB6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit_fournisseur ADD CONSTRAINT FK_48868EB6670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C60A42ED FOREIGN KEY (vente_termine_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat DROP FOREIGN KEY FK_26A98456670C757F');
        $this->addSql('ALTER TABLE commande_achat DROP FOREIGN KEY FK_1FC15B95670C757F');
        $this->addSql('ALTER TABLE detail_achat DROP FOREIGN KEY FK_5B594F0FFE95D117');
        $this->addSql('ALTER TABLE detail_achat DROP FOREIGN KEY FK_5B594F0FF347EFB');
        $this->addSql('ALTER TABLE detail_vente DROP FOREIGN KEY FK_F57AE1157DC7170A');
        $this->addSql('ALTER TABLE detail_vente DROP FOREIGN KEY FK_F57AE115F347EFB');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74B82EA2E54');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74BF347EFB');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EBF347EFB');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EBFE95D117');
        $this->addSql('ALTER TABLE mouvement_stock DROP FOREIGN KEY FK_61E2C8EB7DC7170A');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2791FDB457');
        $this->addSql('ALTER TABLE produit_fournisseur DROP FOREIGN KEY FK_48868EB6F347EFB');
        $this->addSql('ALTER TABLE produit_fournisseur DROP FOREIGN KEY FK_48868EB6670C757F');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C60A42ED');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C19EB6921');
        $this->addSql('DROP TABLE achat');
        $this->addSql('DROP TABLE categorie_produit');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commande_achat');
        $this->addSql('DROP TABLE detail_achat');
        $this->addSql('DROP TABLE detail_vente');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE ligne_commande');
        $this->addSql('DROP TABLE mouvement_stock');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_fournisseur');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE vente');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
