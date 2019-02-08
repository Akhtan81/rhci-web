<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190208152110 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_fc59c1a45e237e06');
        $this->addSql('ALTER TABLE geo_countries ADD currency VARCHAR(5) DEFAULT NULL');
        $this->addSql('UPDATE geo_countries SET currency = \'USD\'');
        $this->addSql('ALTER TABLE geo_countries ALTER currency SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE geo_countries ADD locale VARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE geo_countries ADD name TEXT NOT NULL');
        $this->addSql('ALTER TABLE geo_countries DROP currency');
        $this->addSql('CREATE UNIQUE INDEX uniq_fc59c1a45e237e06 ON geo_countries (name)');
    }
}
