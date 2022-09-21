<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220921164210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organization ADD send_batch_mail_notification TINYINT(1) NOT NULL DEFAULT FALSE');
        $this->addSql(
            'UPDATE organization AS o INNER JOIN organization_detail AS od ON od.id = o.accepted_organization_details_id INNER JOIN session AS s ON s.organization_id = o.id SET o.send_batch_mail_notification = TRUE WHERE s.accepted_details_id IS NOT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organization DROP send_batch_mail_notification');
    }
}
