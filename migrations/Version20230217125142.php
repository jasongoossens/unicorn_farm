<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230217125142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, unicorn_id INT DEFAULT NULL, body LONGTEXT NOT NULL, deleted TINYINT(1) NOT NULL DEFAULT 0, INDEX IDX_5A8A6C8DA76ED395 (user_id), INDEX IDX_5A8A6C8D2AF80346 (unicorn_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unicorn (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_58FBD83FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D2AF80346 FOREIGN KEY (unicorn_id) REFERENCES unicorn (id)');
        $this->addSql('ALTER TABLE unicorn ADD CONSTRAINT FK_58FBD83FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D2AF80346');
        $this->addSql('ALTER TABLE unicorn DROP FOREIGN KEY FK_58FBD83FA76ED395');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE unicorn');
        $this->addSql('DROP TABLE user');
    }
}
