<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240625140335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_60349993F347EFB ON contrat (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBB1823061F');
        $this->addSql('ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBBBF396750');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F347EFB');
        $this->addSql('DROP INDEX IDX_60349993F347EFB ON contrat');
    }
}
