<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180829020834 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 0, \'recycling\', \'kz\', \'Eyeglasses\', null, null, false, false),
  (now(), 1, 0, \'recycling\', \'kz\', \'Used Motor Oil\', null, null, true, false),
  (now(), 2, 0, \'recycling\', \'kz\', \'Used Cooking Oil\', null, null, true, false),
  (now(), 3, 0, \'recycling\', \'kz\', \'Toner Cartridges\', null, null, false, false),
  (now(), 4, 0, \'recycling\', \'kz\', \'Mattresses\', null, null, true, false),
  (now(), 5, 0, \'recycling\', \'kz\', \'Electronic Waste\', null, null, false, false),
  (now(), 6, 0, \'recycling\', \'kz\', \'Food leftovers\', null, null, false, false),
  (now(), 7, 0, \'recycling\', \'kz\', \'Used tires\', null, null, true, false),
  (now(), 8, 0, \'recycling\', \'kz\', \'Household Hazardous Waste\', null, null, true, false),
  (now(), 9, 0, \'recycling\', \'kz\', \'Used diapers\', null, null, true, false),
  (now(), 10, 0, \'recycling\', \'kz\', \'Clothing\', null, null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'kz\', \'Used eyeglasses\', (SELECT id FROM categories WHERE name = \'Eyeglasses\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'kz\', \'Frames\', (SELECT id FROM categories WHERE name = \'Eyeglasses\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'kz\', \'Used printer & toner cartridges\', (SELECT id FROM categories WHERE name = \'Toner Cartridges\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('
INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'kz\', \'Computer equipment\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'kz\', \'VCR\'\'s, DVD players\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 2, 1, \'recycling\', \'kz\', \'Phones\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 3, 1, \'recycling\', \'kz\', \'Televisions\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 4, 1, \'recycling\', \'kz\', \'Microwaves\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'kz\', \'Fish\', (SELECT id FROM categories WHERE name = \'Food leftovers\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'kz\', \'Others\', (SELECT id FROM categories WHERE name = \'Food leftovers\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'kz\', \'Fluorescent lights\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'kz\', \'Batteries\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 2, 1, \'recycling\', \'kz\', \'Poisons\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 3, 1, \'recycling\', \'kz\', \'Pesticides\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 4, 1, \'recycling\', \'kz\', \'Paint\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 5, 1, \'recycling\', \'kz\', \'Expired medication\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 6, 1, \'recycling\', \'kz\', \'Sharps\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'recycling\' LIMIT 1), null, true, false)');


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
    }
}
