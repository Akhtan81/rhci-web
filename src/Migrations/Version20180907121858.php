<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180907121858 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO geo_countries (created_at, locale, name) VALUES
 (NOW(), 'en', 'USA'),
 (NOW(), 'en', 'Kazahstan'),
 (NOW(), 'en', 'Russia')");

        $this->addSql("INSERT INTO geo_regions (created_at, locale, name, country_id, full_name) VALUES
 (NOW(), 'en', 'California', (SELECT id FROM geo_countries WHERE locale = 'en' AND name = 'USA'), 'USA, California')");

        $this->addSql("INSERT INTO geo_cities (created_at, locale, name, region_id, full_name) VALUES
 (NOW(), 'en', 'Alameda', (SELECT id FROM geo_regions WHERE locale = 'en' AND name = 'California' AND country_id = (SELECT id FROM geo_countries WHERE locale = 'en' AND name = 'USA')), 'USA, California, Alameda')");

        $this->addSql("INSERT INTO geo_districts (created_at, locale, name, city_id, full_name, postal_code) VALUES
 (NOW(), 'en', 'Berkeley', (SELECT id FROM geo_cities WHERE locale = 'en' AND name = 'Alameda' AND region_id = (
 SELECT id FROM geo_regions WHERE locale = 'en' AND name = 'California' AND country_id = (
 SELECT id FROM geo_countries WHERE locale = 'en' AND name = 'USA'))), 'USA, California, Alameda, Berkeley', '00000')");
    }

    public function down(Schema $schema): void
    {

    }
}
