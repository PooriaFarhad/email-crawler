<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201012204449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, url_id INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_E7927C7481CFDAE7 (url_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, host VARCHAR(255) NOT NULL, status ENUM(\'new\', \'processing\', \'processed\') NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, request_id INT NOT NULL, url VARCHAR(255) NOT NULL, reference_id INT DEFAULT NULL, crawled_at DATETIME DEFAULT NULL, INDEX IDX_F47645AE427EB8A5 (request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C7481CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id)');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AE427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AE427EB8A5');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C7481CFDAE7');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE url');
    }
}
