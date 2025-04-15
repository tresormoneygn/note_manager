<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415140113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE etudiant (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(12) NOT NULL, nom VARCHAR(45) NOT NULL, prenom VARCHAR(45) NOT NULL, date_naissance VARCHAR(45) DEFAULT NULL, email VARCHAR(45) DEFAULT NULL, numero VARCHAR(45) DEFAULT NULL, addresse VARCHAR(255) DEFAULT NULL, photo VARCHAR(45) DEFAULT NULL, lieu_naissance VARCHAR(45) DEFAULT NULL, pere VARCHAR(45) DEFAULT NULL, mere VARCHAR(45) DEFAULT NULL, nom_tuteur VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE etudiant
        SQL);
    }
}
