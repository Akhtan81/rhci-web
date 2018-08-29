<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829012708 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 0, \'junk_removal\', \'en\', \'Furniture\', null, null, false, false),
  (now(), 1, 0, \'junk_removal\', \'en\', \'E-waste\', null, null, false, false),
  (now(), 2, 0, \'junk_removal\', \'en\', \'Appliances\', null, null, false, false),
  (now(), 3, 0, \'junk_removal\', \'en\', \'Mattresses\', null, null, false, false),
  (now(), 4, 0, \'junk_removal\', \'en\', \'Miscellaneous\', null, 3000, true, true),
  (now(), 5, 0, \'junk_removal\', \'en\', \'Trash removal\', null, null, true, false),
  (now(), 6, 0, \'junk_removal\', \'en\', \'Construction waste disposal\', null, null, true, false),
  (now(), 7, 0, \'junk_removal\', \'en\', \'Yard waste removal\', null, null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'en\', \'Loveseat\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 1, 1, \'junk_removal\', \'en\', \'Sofa/couch\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 2, 1, \'junk_removal\', \'en\', \'Sectional 2 or 3 piece\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 7000, true, true),
  (now(), 3, 1, \'junk_removal\', \'en\', \'Recliner\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 7000, true, true),
  (now(), 4, 1, \'junk_removal\', \'en\', \'Sleeper sofa\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 5, 1, \'junk_removal\', \'en\', \'Chairs\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 4000, true, true),
  (now(), 6, 1, \'junk_removal\', \'en\', \'Table\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 7, 1, \'junk_removal\', \'en\', \'Desk\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 4000, true, true),
  (now(), 8, 1, \'junk_removal\', \'en\', \'Bookcase\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 4000, true, true),
  (now(), 9, 1, \'junk_removal\', \'en\', \'Dresser\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 10, 1, \'junk_removal\', \'en\', \'Bed\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 11, 1, \'junk_removal\', \'en\', \'Take a part\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 LIMIT 1), 2000, true, true);');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'en\', \'Computer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 LIMIT 1), 4000, true, true),
  (now(), 1, 1, \'junk_removal\', \'en\', \'Desktop printer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 LIMIT 1), 4000, true, true),
  (now(), 2, 1, \'junk_removal\', \'en\', \'Big printer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 LIMIT 1), 7000, true, true),
  (now(), 3, 1, \'junk_removal\', \'en\', \'TV\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 LIMIT 1), null, false, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 2, \'junk_removal\', \'en\', \'Small\', (SELECT id FROM categories WHERE name = \'TV\' AND lvl = 1 LIMIT 1), 4000, true, true),
  (now(), 1, 2, \'junk_removal\', \'en\', \'Large\', (SELECT id FROM categories WHERE name = \'TV\' AND lvl = 1 LIMIT 1), 5000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'en\', \'Refrigerator\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 LIMIT 1), null, false, false),
  (now(), 1, 1, \'junk_removal\', \'en\', \'Washer\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 2, 1, \'junk_removal\', \'en\', \'Dryer\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 LIMIT 1), 4000, true, true),
  (now(), 3, 1, \'junk_removal\', \'en\', \'Stove\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 4, 1, \'junk_removal\', \'en\', \'Grill/bbq\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 LIMIT 1), 5000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 2, \'junk_removal\', \'en\', \'Small\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 LIMIT 1), 4000, true, true),
  (now(), 1, 2, \'junk_removal\', \'en\', \'Standard\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 LIMIT 1), 5000, true, true),
  (now(), 2, 2, \'junk_removal\', \'en\', \'Large\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 LIMIT 1), 6000, true, true),
  (now(), 3, 2, \'junk_removal\', \'en\', \'Side by side\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 LIMIT 1), 7000, true, true),
  (now(), 4, 2, \'junk_removal\', \'en\', \'Doors take of\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 LIMIT 1), 2000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'en\', \'Twin\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 LIMIT 1), 4000, true, true),
  (now(), 0, 1, \'junk_removal\', \'en\', \'Queen/king\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 LIMIT 1), 5000, true, true),
  (now(), 0, 1, \'junk_removal\', \'en\', \'Box spring\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 LIMIT 1), 4000, true, true)');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
