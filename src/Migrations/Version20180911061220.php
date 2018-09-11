<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\PaymentProvider;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180911061220 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("ALTER TABLE partners ADD provider VARCHAR(32) NOT NULL DEFAULT '" . PaymentProvider::STRIPE . "'");
        $this->addSql('ALTER TABLE partners ADD account_id TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE partners ALTER provider DROP DEFAULT');


        $this->addSql('ALTER TABLE credit_cards ADD currency VARCHAR(12) DEFAULT NULL');
        $this->addSql('ALTER TABLE credit_cards ADD type TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD primary_credit_card_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9C5F71852 FOREIGN KEY (primary_credit_card_id) REFERENCES credit_cards (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C5F71852 ON users (primary_credit_card_id)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE partners DROP provider');
        $this->addSql('ALTER TABLE partners DROP account_id');
    }
}
