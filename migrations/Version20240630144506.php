<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240630144506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, adminenti VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, id_utilisateur VARCHAR(255) NOT NULL, name_utilisateur VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, gg VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, utilisateurs VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE contrat RENAME INDEX produit_id TO IDX_60349993F347EFB');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY fk_contrat_id');
        $this->addSql('DROP INDEX fk_contrat_id ON produit');
        $this->addSql('ALTER TABLE produit DROP contrat_id');
        $this->addSql('ALTER TABLE user ADD is_active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('ALTER TABLE user DROP is_active');
        $this->addSql('ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBB1823061F');
        $this->addSql('ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBBBF396750');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F347EFB');
        $this->addSql('ALTER TABLE contrat RENAME INDEX idx_60349993f347efb TO produit_id');
        $this->addSql('ALTER TABLE produit ADD contrat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT fk_contrat_id FOREIGN KEY (contrat_id) REFERENCES contrat (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX fk_contrat_id ON produit (contrat_id)');
    }
}
