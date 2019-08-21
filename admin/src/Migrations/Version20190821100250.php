<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190821100250 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO geo_country_translations(country_id, created_at, locale, name, alt_name) VALUES 
            ((SELECT id FROM geo_countries WHERE currency = 'USD' LIMIT 1), now(), 'ru', 'Соединенные Штаты Америки', 'США'),
            ((SELECT id FROM geo_countries WHERE currency = 'USD' LIMIT 1), now(), 'kz', 'Америка Құрама Штаттары', 'АҚШ'),
            ((SELECT id FROM geo_countries WHERE currency = 'USD' LIMIT 1), now(), 'en', 'United States of America', 'USA'),
            ((SELECT id FROM geo_countries WHERE currency = 'USD' LIMIT 1), now(), 'en', 'United States', 'US'),
            ((SELECT id FROM geo_countries WHERE currency = 'USD' LIMIT 1), now(), 'es', 'Estados Unidos', 'EU')
        ");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
    }
}
