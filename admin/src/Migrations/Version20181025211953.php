<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181025211953 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE partner_subscriptions (id SERIAL NOT NULL, partner_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_488FB7409393F8FE ON partner_subscriptions (partner_id)');
        $this->addSql('ALTER TABLE partner_subscriptions ADD CONSTRAINT FK_488FB7409393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partners DROP card_response');
        $this->addSql('ALTER TABLE partner_subscriptions ADD provider_id TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE partner_subscriptions ADD provider_response TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE partner_subscriptions ADD status VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE partner_subscriptions ADD type VARCHAR(16) NOT NULL');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE partner_subscriptions');
        $this->addSql('ALTER TABLE partners ADD card_response TEXT DEFAULT NULL');
    }
}
