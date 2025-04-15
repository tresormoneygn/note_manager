<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414235957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_fonction (user_id INT NOT NULL, fonction_id INT NOT NULL, INDEX IDX_E98D31BDA76ED395 (user_id), INDEX IDX_E98D31BD57889920 (fonction_id), PRIMARY KEY(user_id, fonction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_fonction ADD CONSTRAINT FK_E98D31BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_fonction ADD CONSTRAINT FK_E98D31BD57889920 FOREIGN KEY (fonction_id) REFERENCES fonction (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_fonction DROP FOREIGN KEY FK_E98D31BDA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_fonction DROP FOREIGN KEY FK_E98D31BD57889920
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_fonction
        SQL);
    }
}
