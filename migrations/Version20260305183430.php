<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305183430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout dans la table tache des champs pour statistique';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache ADD date_debut DATETIME DEFAULT NULL, ADD date_fin DATETIME DEFAULT NULL, ADD duree INT DEFAULT NULL, ADD duree_str VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720756BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075BCF5E72D');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720756BF700BD');
        $this->addSql('ALTER TABLE tache DROP date_debut, DROP date_fin, DROP duree, DROP duree_str');
    }
}
