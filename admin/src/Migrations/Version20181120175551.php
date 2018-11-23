<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181120175551 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE units (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX unq_units ON units (name, locale)');
        $this->addSql('ALTER TABLE partner_categories ADD unit_id INT NOT NULL');
        $this->addSql('ALTER TABLE partner_categories ADD min_amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partner_categories ADD CONSTRAINT FK_2002458EF8BD700D FOREIGN KEY (unit_id) REFERENCES units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2002458EF8BD700D ON partner_categories (unit_id)');

        $this->addSql('DROP INDEX unq_partner_categories');
        $this->addSql('CREATE UNIQUE INDEX unq_partner_categories ON partner_categories (partner_id, category_id, unit_id, min_amount)');
        $this->addSql('DROP INDEX unq_partner_postal_codes');


    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE units');
    }
}
