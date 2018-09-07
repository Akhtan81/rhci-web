<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180829015717 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 0, \'junk_removal\', \'ru\', \'Furniture\', null, null, false, false),
  (now(), 1, 0, \'junk_removal\', \'ru\', \'E-waste\', null, null, false, false),
  (now(), 2, 0, \'junk_removal\', \'ru\', \'Appliances\', null, null, false, false),
  (now(), 3, 0, \'junk_removal\', \'ru\', \'Mattresses\', null, null, false, false),
  (now(), 4, 0, \'junk_removal\', \'ru\', \'Miscellaneous\', null, 3000, true, true),
  (now(), 5, 0, \'junk_removal\', \'ru\', \'Trash removal\', null, null, true, false),
  (now(), 6, 0, \'junk_removal\', \'ru\', \'Construction waste disposal\', null, null, true, false),
  (now(), 7, 0, \'junk_removal\', \'ru\', \'Yard waste removal\', null, null, true, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'ru\', \'Loveseat\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 1, 1, \'junk_removal\', \'ru\', \'Sofa/couch\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 2, 1, \'junk_removal\', \'ru\', \'Sectional 2 or 3 piece\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 3, 1, \'junk_removal\', \'ru\', \'Recliner\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 4, 1, \'junk_removal\', \'ru\', \'Sleeper sofa\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 5, 1, \'junk_removal\', \'ru\', \'Chairs\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 6, 1, \'junk_removal\', \'ru\', \'Table\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 7, 1, \'junk_removal\', \'ru\', \'Desk\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 8, 1, \'junk_removal\', \'ru\', \'Bookcase\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 9, 1, \'junk_removal\', \'ru\', \'Dresser\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 10, 1, \'junk_removal\', \'ru\', \'Bed\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 11, 1, \'junk_removal\', \'ru\', \'Take a part\', (SELECT id FROM categories WHERE name = \'Furniture\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 2000, true, true);');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'ru\', \'Computer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 1, 1, \'junk_removal\', \'ru\', \'Desktop printer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 2, 1, \'junk_removal\', \'ru\', \'Big printer\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 3, 1, \'junk_removal\', \'ru\', \'TV\', (SELECT id FROM categories WHERE name = \'E-waste\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), null, false, false)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 2, \'junk_removal\', \'ru\', \'Small\', (SELECT id FROM categories WHERE name = \'TV\' AND lvl = 1 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 1, 2, \'junk_removal\', \'ru\', \'Large\', (SELECT id FROM categories WHERE name = \'TV\' AND lvl = 1 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'ru\', \'Refrigerator\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), null, false, false),
  (now(), 1, 1, \'junk_removal\', \'ru\', \'Washer\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 2, 1, \'junk_removal\', \'ru\', \'Dryer\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 3, 1, \'junk_removal\', \'ru\', \'Stove\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 4, 1, \'junk_removal\', \'ru\', \'Grill/bbq\', (SELECT id FROM categories WHERE name = \'Appliances\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 2, \'junk_removal\', \'ru\', \'Small\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 1, 2, \'junk_removal\', \'ru\', \'Standard\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 2, 2, \'junk_removal\', \'ru\', \'Large\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 6000, true, true),
  (now(), 3, 2, \'junk_removal\', \'ru\', \'Side by side\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 7000, true, true),
  (now(), 4, 2, \'junk_removal\', \'ru\', \'Doors take of\', (SELECT id FROM categories WHERE name = \'Refrigerator\' AND lvl = 1 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 2000, true, true)');

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 1, \'junk_removal\', \'ru\', \'Twin\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true),
  (now(), 0, 1, \'junk_removal\', \'ru\', \'Queen/king\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 5000, true, true),
  (now(), 0, 1, \'junk_removal\', \'ru\', \'Box spring\', (SELECT id FROM categories WHERE name = \'Mattresses\' AND lvl = 0 AND locale = \'ru\' AND type = \'junk_removal\' LIMIT 1), 4000, true, true)');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
    }
}
