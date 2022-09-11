<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220911160039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apprenticeship (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, proposed_details_id INT NOT NULL, accepted_details_id INT DEFAULT NULL, INDEX IDX_6904B3757E3C61F9 (owner_id), UNIQUE INDEX UNIQ_6904B375F9D283C (proposed_details_id), UNIQUE INDEX UNIQ_6904B375768D045C (accepted_details_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apprenticeship_detail (id INT AUTO_INCREMENT NOT NULL, location_lat DOUBLE PRECISION NOT NULL, location_lng DOUBLE PRECISION NOT NULL, jobs LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', location_name VARCHAR(255) NOT NULL, location_street_no VARCHAR(255) NOT NULL, location_zipcode VARCHAR(255) NOT NULL, location_city VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apprenticeship ADD CONSTRAINT FK_6904B3757E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE apprenticeship ADD CONSTRAINT FK_6904B375F9D283C FOREIGN KEY (proposed_details_id) REFERENCES apprenticeship_detail (id)');
        $this->addSql('ALTER TABLE apprenticeship ADD CONSTRAINT FK_6904B375768D045C FOREIGN KEY (accepted_details_id) REFERENCES apprenticeship_detail (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apprenticeship DROP FOREIGN KEY FK_6904B375F9D283C');
        $this->addSql('ALTER TABLE apprenticeship DROP FOREIGN KEY FK_6904B375768D045C');
        $this->addSql('DROP TABLE apprenticeship');
        $this->addSql('DROP TABLE apprenticeship_detail');
    }
}
