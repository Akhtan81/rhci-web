<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180829020833 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 0, \'recycling\', \'ru\', \'Eyeglasses\', null, null, false, false),
  (now(), 1, 0, \'recycling\', \'ru\', \'Used Motor Oil\', null, null, true, false),
  (now(), 2, 0, \'recycling\', \'ru\', \'Used Cooking Oil\', null, null, true, false),
  (now(), 3, 0, \'recycling\', \'ru\', \'Toner Cartridges\', null, null, false, false),
  (now(), 4, 0, \'recycling\', \'ru\', \'Mattresses\', null, null, false, false),
  (now(), 5, 0, \'recycling\', \'ru\', \'Electronic Waste\', null, null, false, false),
  (now(), 6, 0, \'recycling\', \'ru\', \'Food leftovers\', null, null, false, false),
  (now(), 7, 0, \'recycling\', \'ru\', \'Used tires\', null, null, false, false),
  (now(), 8, 0, \'recycling\', \'ru\', \'Household Hazardous Waste\', null, null, true, false),
  (now(), 9, 0, \'recycling\', \'ru\', \'Used diapers\', null, null, false, false),
  (now(), 10, 0, \'recycling\', \'ru\', \'Clothing\', null, null, false, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'ru\', \'Used eyeglasses\', (SELECT id FROM categories WHERE name = \'Eyeglasses\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'ru\', \'Frames\', (SELECT id FROM categories WHERE name = \'Eyeglasses\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'ru\', \'Used printer & toner cartridges\', (SELECT id FROM categories WHERE name = \'Toner Cartridges\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('
INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'ru\', \'Computer equipment\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'ru\', \'VCR\'\'s, DVD players\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 2, 1, \'recycling\', \'ru\', \'Phones\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 3, 1, \'recycling\', \'ru\', \'Televisions\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 4, 1, \'recycling\', \'ru\', \'Microwaves\', (SELECT id FROM categories WHERE name = \'Electronic Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'ru\', \'Fish\', (SELECT id FROM categories WHERE name = \'Food leftovers\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'ru\', \'Others\', (SELECT id FROM categories WHERE name = \'Food leftovers\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'recycling\', \'ru\', \'Fluorescent lights\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 1, 1, \'recycling\', \'ru\', \'Batteries\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 2, 1, \'recycling\', \'ru\', \'Poisons\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 3, 1, \'recycling\', \'ru\', \'Pesticides\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 4, 1, \'recycling\', \'ru\', \'Paint\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 5, 1, \'recycling\', \'ru\', \'Expired medication\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false),
  (now(), 6, 1, \'recycling\', \'ru\', \'Sharps\', (SELECT id FROM categories WHERE name = \'Household Hazardous Waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'recycling\' LIMIT 1), null, true, false)');


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
    }
}
