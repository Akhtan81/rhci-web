<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829042109 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO geo_countries (created_at, locale, name) VALUES
 (NOW(), 'en', 'USA'),
 (NOW(), 'en', 'Kazahstan'),
 (NOW(), 'en', 'Russia')");
    }

    public function down(Schema $schema): void
    {

    }
}
