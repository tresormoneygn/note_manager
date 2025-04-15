<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415140309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, etudiant_id INT NOT NULL, annee_id INT NOT NULL, programme_id INT NOT NULL, INDEX IDX_5E90F6D68F5EA509 (classe_id), INDEX IDX_5E90F6D6DDEAB1A3 (etudiant_id), INDEX IDX_5E90F6D6543EC5F0 (annee_id), INDEX IDX_5E90F6D662BB7AEE (programme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D68F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6543EC5F0 FOREIGN KEY (annee_id) REFERENCES annee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D662BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D68F5EA509
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6DDEAB1A3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6543EC5F0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D662BB7AEE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE inscription
        SQL);
    }
}
