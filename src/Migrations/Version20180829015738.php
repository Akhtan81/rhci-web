<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829015738 extends AbstractMigration
{
    public function up(Schema $schema): void
    {

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 0, \'junk_removal\', \'kz\', \'Furniture\', null, null, false, false),
  (now(), 1, 0, \'junk_removal\', \'kz\', \'E-waste\', null, null, false, false),
  (now(), 2, 0, \'junk_removal\', \'kz\', \'Appliances\', null, null, false, false),
  (now(), 3, 0, \'junk_removal\', \'kz\', \'Mattresses\', null, null, false, false),
  (now(), 4, 0, \'junk_removal\', \'kz\', \'Miscellaneous\', null, 3000, true, true),
  (now(), 5, 0, \'junk_removal\', \'kz\', \'Trash removal\', null, null, true, false),
  (now(), 6, 0, \'junk_removal\', \'kz\', \'Construction waste disposal\', null, null, true, false),
  (now(), 7, 0, \'junk_removal\', \'kz\', \'Yard waste removal\', null, null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'kz\', \'Loveseat\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 1, 1, \'junk_removal\', \'kz\', \'Sofa/couch\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 2, 1, \'junk_removal\', \'kz\', \'Sectional 2 or 3 piece\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 3, 1, \'junk_removal\', \'kz\', \'Recliner\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 4, 1, \'junk_removal\', \'kz\', \'Sleeper sofa\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 5, 1, \'junk_removal\', \'kz\', \'Chairs\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 6, 1, \'junk_removal\', \'kz\', \'Table\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 7, 1, \'junk_removal\', \'kz\', \'Desk\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 8, 1, \'junk_removal\', \'kz\', \'Bookcase\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 9, 1, \'junk_removal\', \'kz\', \'Dresser\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 10, 1, \'junk_removal\', \'kz\', \'Bed\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 11, 1, \'junk_removal\', \'kz\', \'Take a part\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 2000, true, true);');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'kz\', \'Computer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 1, 1, \'junk_removal\', \'kz\', \'Desktop printer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 2, 1, \'junk_removal\', \'kz\', \'Big printer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 3, 1, \'junk_removal\', \'kz\', \'TV\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), null, false, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 2, \'junk_removal\', \'kz\', \'Small\', (SELECT id FROM categories WHERE name = \'TV\' AND lvl = 1 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 1, 2, \'junk_removal\', \'kz\', \'Large\', (SELECT id FROM categories WHERE name = \'TV\' AND lvl = 1 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'kz\', \'Refrigerator\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), null, false, false),
  (now(), 1, 1, \'junk_removal\', \'kz\', \'Washer\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 2, 1, \'junk_removal\', \'kz\', \'Dryer\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 3, 1, \'junk_removal\', \'kz\', \'Stove\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 4, 1, \'junk_removal\', \'kz\', \'Grill/bbq\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 2, \'junk_removal\', \'kz\', \'Small\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 1, 2, \'junk_removal\', \'kz\', \'Standard\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 2, 2, \'junk_removal\', \'kz\', \'Large\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 6000, true, true),
  (now(), 3, 2, \'junk_removal\', \'kz\', \'Side by side\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 4, 2, \'junk_removal\', \'kz\', \'Doors take of\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 2000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'kz\', \'Twin\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 0, 1, \'junk_removal\', \'kz\', \'Queen/king\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 0, 1, \'junk_removal\', \'kz\', \'Box spring\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 AND locale = \'kz\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
    }
}
