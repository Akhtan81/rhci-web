<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180921215322 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE credit_cards ALTER is_primary DROP DEFAULT');
        $this->addSql('ALTER TABLE users ADD password_token VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD password_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9BEAB6C24 ON users (password_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE credit_cards ALTER is_primary SET DEFAULT \'false\'');
        $this->addSql('DROP INDEX UNIQ_1483A5E9BEAB6C24');
        $this->addSql('ALTER TABLE users DROP password_token');
        $this->addSql('ALTER TABLE users DROP password_token_expires_at');
    }
}
