<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180928155216 extends AbstractMigration
{
    public function up(Schema $schema): void
    {

        $this->addSql('DELETE FROM user_locations where location_id in (SELECT id FROM locations where postal_code is null)');
        $this->addSql('UPDATE users SET location_id = NULL where location_id in (SELECT id from user_locations where location_id in (SELECT id FROM locations where postal_code is null))');
        $this->addSql('DELETE FROM locations where postal_code is null');

        $this->addSql('ALTER TABLE locations ADD city TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE locations ALTER postal_code SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE locations DROP city');
        $this->addSql('ALTER TABLE locations ALTER postal_code DROP NOT NULL');
    }
}
