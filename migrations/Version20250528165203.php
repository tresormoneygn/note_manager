<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528165203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etudiant CHANGE date_naissance date_naissance DATE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme ADD user_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3DDCB9FFA76ED395 ON programme (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D64962BB7AEE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8D93D64962BB7AEE ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP programme_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etudiant CHANGE date_naissance date_naissance VARCHAR(45) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3DDCB9FFA76ED395 ON programme
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme DROP user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD programme_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D64962BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D64962BB7AEE ON user (programme_id)
        SQL);
    }
}
