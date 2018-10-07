<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180925171202 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE credit_cards ADD last_four VARCHAR(4)');
        $this->addSql('UPDATE credit_cards SET last_four = substring(name, 0, 4)');
        $this->addSql('ALTER TABLE credit_cards DROP name');
        $this->addSql('ALTER TABLE credit_cards ALTER last_four SET NOT NULL');
    }

    public function down(Schema $schema): void
    {

    }
}
