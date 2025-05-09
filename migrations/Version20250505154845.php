<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505154845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE annee (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, is_progress TINYINT(1) NOT NULL, annee_uuid VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE departement_historique (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, user_id INT NOT NULL, annee_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_338355E4CCF9E01E (departement_id), INDEX IDX_338355E4A76ED395 (user_id), INDEX IDX_338355E4543EC5F0 (annee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE etudiant (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(12) NOT NULL, nom VARCHAR(45) NOT NULL, prenom VARCHAR(45) NOT NULL, date_naissance VARCHAR(45) DEFAULT NULL, email VARCHAR(45) DEFAULT NULL, numero VARCHAR(45) DEFAULT NULL, addresse VARCHAR(255) DEFAULT NULL, photo VARCHAR(45) DEFAULT NULL, lieu_naissance VARCHAR(45) DEFAULT NULL, pere VARCHAR(45) DEFAULT NULL, mere VARCHAR(45) DEFAULT NULL, nom_tuteur VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fonction (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, label VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, etudiant_id INT NOT NULL, annee_id INT NOT NULL, programme_id INT NOT NULL, INDEX IDX_5E90F6D68F5EA509 (classe_id), INDEX IDX_5E90F6D6DDEAB1A3 (etudiant_id), INDEX IDX_5E90F6D6543EC5F0 (annee_id), INDEX IDX_5E90F6D662BB7AEE (programme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, unite_enseignement_id INT NOT NULL, name VARCHAR(45) NOT NULL, coefficient INT NOT NULL, INDEX IDX_9014574A18DEEBA5 (unite_enseignement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, matiere_id INT DEFAULT NULL, student_id INT NOT NULL, note_1 DOUBLE PRECISION NOT NULL, note_2 DOUBLE PRECISION NOT NULL, note_3 DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', max_delay DATETIME NOT NULL, INDEX IDX_CFBDFA14F46CD258 (matiere_id), INDEX IDX_CFBDFA14CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE programme (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, annee_id INT NOT NULL, name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', label VARCHAR(50) NOT NULL, INDEX IDX_3DDCB9FFCCF9E01E (departement_id), INDEX IDX_3DDCB9FF543EC5F0 (annee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rapport (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, filename VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_BE34A09CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE semestre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, label VARCHAR(55) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE unite_enseignement (id INT AUTO_INCREMENT NOT NULL, programme_id INT NOT NULL, semestre_id INT NOT NULL, nom VARCHAR(45) NOT NULL, coefficient INT NOT NULL, label VARCHAR(50) NOT NULL, INDEX IDX_46D07C4F62BB7AEE (programme_id), INDEX IDX_46D07C4F5577AFDB (semestre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, rapport_id INT DEFAULT NULL, programme_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, date_naissance DATE NOT NULL, matricule VARCHAR(50) DEFAULT NULL, numero VARCHAR(15) NOT NULL, addresse VARCHAR(50) NOT NULL, is_verified TINYINT(1) NOT NULL, INDEX IDX_8D93D6491DFBCC46 (rapport_id), INDEX IDX_8D93D64962BB7AEE (programme_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_fonction (user_id INT NOT NULL, fonction_id INT NOT NULL, INDEX IDX_E98D31BDA76ED395 (user_id), INDEX IDX_E98D31BD57889920 (fonction_id), PRIMARY KEY(user_id, fonction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
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
        $this->addSql(<<<'SQL'
            ALTER TABLE matiere ADD CONSTRAINT FK_9014574A18DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14CB944F1A FOREIGN KEY (student_id) REFERENCES etudiant (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FFCCF9E01E FOREIGN KEY (departement_id) REFERENCES departement_historique (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme ADD CONSTRAINT FK_3DDCB9FF543EC5F0 FOREIGN KEY (annee_id) REFERENCES annee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4F62BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4F5577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D6491DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D64962BB7AEE FOREIGN KEY (programme_id) REFERENCES programme (id)
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
            ALTER TABLE departement_historique DROP FOREIGN KEY FK_338355E4CCF9E01E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique DROP FOREIGN KEY FK_338355E4A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE departement_historique DROP FOREIGN KEY FK_338355E4543EC5F0
        SQL);
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
            ALTER TABLE matiere DROP FOREIGN KEY FK_9014574A18DEEBA5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14F46CD258
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14CB944F1A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FFCCF9E01E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programme DROP FOREIGN KEY FK_3DDCB9FF543EC5F0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09CA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F62BB7AEE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F5577AFDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491DFBCC46
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D64962BB7AEE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_fonction DROP FOREIGN KEY FK_E98D31BDA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_fonction DROP FOREIGN KEY FK_E98D31BD57889920
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE annee
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
        $this->addSql(<<<'SQL'
            DROP TABLE etudiant
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fonction
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE inscription
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE matiere
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE note
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE programme
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rapport
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE semestre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE unite_enseignement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_fonction
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
