<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190209112813 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE units DROP locale');
        $this->addSql('ALTER TABLE units DROP name');
        $this->addSql('ALTER TABLE categories DROP locale');
        $this->addSql('ALTER TABLE categories DROP name');
        $this->addSql('ALTER TABLE geo_countries DROP locale');
        $this->addSql('ALTER TABLE geo_countries DROP name');

        $this->addSql('ALTER TABLE geo_countries ALTER currency SET NOT NULL');
        $this->addSql('ALTER TABLE geo_country_translations ADD alt_name TEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE geo_countries ADD locale VARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE geo_countries ADD name TEXT NOT NULL');
        $this->addSql('ALTER TABLE geo_countries ALTER currency DROP NOT NULL');
        $this->addSql('ALTER TABLE geo_country_translations DROP alt_name');
    }
}
