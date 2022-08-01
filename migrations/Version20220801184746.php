<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220801184746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE session_detail ADD channel_id INT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE session_detail ADD CONSTRAINT FK_416D75CA72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)'
        );
        $this->addSql('CREATE INDEX IDX_416D75CA72F5A1AA ON session_detail (channel_id)');

        $this->addSql(
            "INSERT INTO channel (name) VALUES ('Science'), ('Tech & Nachhaltigkeit'), ('Digitale Gesellschaft'), ('eCommerce'), ('Consulting')"
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session_detail DROP FOREIGN KEY FK_416D75CA72F5A1AA');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP INDEX IDX_416D75CA72F5A1AA ON session_detail');
        $this->addSql('ALTER TABLE session_detail DROP channel_id');
    }
}
