<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415125233 extends AbstractMigration
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
            CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE departement_historique (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, user_id INT NOT NULL, annee_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_338355E4CCF9E01E (departement_id), INDEX IDX_338355E4A76ED395 (user_id), INDEX IDX_338355E4543EC5F0 (annee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique ADD CONSTRAINT FK_338355E4CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique ADD CONSTRAINT FK_338355E4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique ADD CONSTRAINT FK_338355E4543EC5F0 FOREIGN KEY (annee_id) REFERENCES annee (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique DROP FOREIGN KEY FK_338355E4CCF9E01E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique DROP FOREIGN KEY FK_338355E4A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique DROP FOREIGN KEY FK_338355E4543EC5F0
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE classe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE departement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE departement_historique
        SQL);
    }
}
