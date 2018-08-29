<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180829011214 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE categories ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categories ADD lvl INT NOT NULL');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('ALTER TABLE categories ADD is_selectable BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE categories ADD price INT');
        $this->addSql('ALTER TABLE categories ADD type VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE categories ADD has_price BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE categories ADD ordering INT NOT NULL');
        $this->addSql('DROP INDEX uniq_3af346685e237e06');
        $this->addSql('CREATE UNIQUE INDEX unq_categories ON categories (name, parent_id, locale)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categories DROP CONSTRAINT FK_3AF34668727ACA70');
        $this->addSql('DROP INDEX IDX_3AF34668727ACA70');
        $this->addSql('ALTER TABLE categories DROP parent_id');
        $this->addSql('ALTER TABLE categories DROP lvl');
    }
}
