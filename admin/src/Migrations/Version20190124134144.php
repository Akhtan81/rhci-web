<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190124134144 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("DROP TABLE geo_cities CASCADE ");
        $this->addSql("DROP TABLE geo_districts");
        $this->addSql("DROP TABLE geo_regions");

        $this->addSql("ALTER TABLE requested_categories DROP deleted_at");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
