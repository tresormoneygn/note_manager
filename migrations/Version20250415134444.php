<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415134444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, unite_enseignement_id INT NOT NULL, name VARCHAR(45) NOT NULL, coefficient INT NOT NULL, INDEX IDX_9014574A18DEEBA5 (unite_enseignement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE semestre (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE unite_enseignement (id INT AUTO_INCREMENT NOT NULL, programme_id INT NOT NULL, nom VARCHAR(45) NOT NULL, coefficient INT NOT NULL, INDEX IDX_46D07C4F62BB7AEE (programme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE matiere ADD CONSTRAINT FK_9014574A18DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4F62BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE matiere DROP FOREIGN KEY FK_9014574A18DEEBA5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F62BB7AEE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE classe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE matiere
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE semestre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE unite_enseignement
        SQL);
    }
}
