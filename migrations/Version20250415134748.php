<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415134748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement ADD semestre_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4F5577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_46D07C4F5577AFDB ON unite_enseignement (semestre_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F5577AFDB
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_46D07C4F5577AFDB ON unite_enseignement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement DROP semestre_id
        SQL);
    }
}
