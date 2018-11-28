<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181128204044 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users ALTER is_demo DROP DEFAULT');
        $this->addSql('ALTER TABLE partners ADD can_manage_recycling_orders BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE partners ADD can_manage_junk_removal_orders BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE partners ADD can_manage_donation_orders BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE partners ADD can_manage_shredding_orders BOOLEAN NOT NULL DEFAULT FALSE');

        $this->addSql('UPDATE partners SET can_manage_shredding_orders = TRUE WHERE account_id IS NOT NULL');
        $this->addSql('UPDATE partners SET can_manage_junk_removal_orders = TRUE WHERE account_id IS NOT NULL');
        $this->addSql('UPDATE partners SET can_manage_recycling_orders = TRUE');
        $this->addSql('UPDATE partners SET can_manage_donation_orders = TRUE');

        $this->addSql('ALTER TABLE partners ALTER can_manage_recycling_orders DROP DEFAULT');
        $this->addSql('ALTER TABLE partners ALTER can_manage_junk_removal_orders DROP DEFAULT');
        $this->addSql('ALTER TABLE partners ALTER can_manage_donation_orders DROP DEFAULT');
        $this->addSql('ALTER TABLE partners ALTER can_manage_shredding_orders DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE partners DROP can_manage_recycling_orders');
        $this->addSql('ALTER TABLE partners DROP can_manage_junk_removal_orders');
        $this->addSql('ALTER TABLE partners DROP can_manage_donation_orders');
        $this->addSql('ALTER TABLE partners DROP can_manage_shredding_orders');
        $this->addSql('ALTER TABLE users ALTER is_demo SET DEFAULT \'false\'');
    }
}
