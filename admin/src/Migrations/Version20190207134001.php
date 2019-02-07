<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207134001 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE unit_translations (id SERIAL NOT NULL, unit_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14213810F8BD700D ON unit_translations (unit_id)');
        $this->addSql('CREATE TABLE category_translations (id SERIAL NOT NULL, category_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1C60F91512469DE2 ON category_translations (category_id)');
        $this->addSql('ALTER TABLE unit_translations ADD CONSTRAINT FK_14213810F8BD700D FOREIGN KEY (unit_id) REFERENCES units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_translations ADD CONSTRAINT FK_1C60F91512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX unq_units');

        $this->addSql('ALTER TABLE units ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

//        $this->addSql('ALTER TABLE units DROP locale');
//        $this->addSql('ALTER TABLE units DROP name');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE unit_translations');
        $this->addSql('DROP TABLE category_translations');
        $this->addSql('ALTER TABLE units ADD locale VARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE units ADD name TEXT NOT NULL');
        $this->addSql('ALTER TABLE units DROP deleted_at');
        $this->addSql('CREATE UNIQUE INDEX unq_units ON units (name, locale)');
    }
}
