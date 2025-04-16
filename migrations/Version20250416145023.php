<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416145023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etudiant CHANGE date_naissance date_naissance VARCHAR(45) DEFAULT NULL, CHANGE email email VARCHAR(45) DEFAULT NULL, CHANGE numero numero VARCHAR(45) DEFAULT NULL, CHANGE addresse addresse VARCHAR(255) DEFAULT NULL, CHANGE photo photo VARCHAR(45) DEFAULT NULL, CHANGE lieu_naissance lieu_naissance VARCHAR(45) DEFAULT NULL, CHANGE pere pere VARCHAR(45) DEFAULT NULL, CHANGE mere mere VARCHAR(45) DEFAULT NULL, CHANGE nom_tuteur nom_tuteur VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE programme_id programme_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE matricule matricule VARCHAR(50) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etudiant CHANGE date_naissance date_naissance VARCHAR(45) DEFAULT 'NULL', CHANGE email email VARCHAR(45) DEFAULT 'NULL', CHANGE numero numero VARCHAR(45) DEFAULT 'NULL', CHANGE addresse addresse VARCHAR(255) DEFAULT 'NULL', CHANGE photo photo VARCHAR(45) DEFAULT 'NULL', CHANGE lieu_naissance lieu_naissance VARCHAR(45) DEFAULT 'NULL', CHANGE pere pere VARCHAR(45) DEFAULT 'NULL', CHANGE mere mere VARCHAR(45) DEFAULT 'NULL', CHANGE nom_tuteur nom_tuteur VARCHAR(100) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT 'NULL' COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE programme_id programme_id INT NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE matricule matricule VARCHAR(50) DEFAULT 'NULL'
        SQL);
    }
}
