<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829015738 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {

        $this->addSql('INSERT INTO categories (created_at, ordering, lvl, type, locale, name, parent_id, price, is_selectable, has_price) VALUES
  (now(), 0, 0, \'junk_removal\', \'kz\', \'Мебель\', null, null, false, false),
  (now(), 1, 0, \'junk_removal\', \'kz\', \'Электроника\', null, null, false, false),
  (now(), 2, 0, \'junk_removal\', \'kz\', \'Кухня\', null, null, false, false),
  (now(), 3, 0, \'junk_removal\', \'kz\', \'Матрасы\', null, null, false, false),
  (now(), 4, 0, \'junk_removal\', \'kz\', \'Разное\', null, 3000, true, true),
  (now(), 5, 0, \'junk_removal\', \'kz\', \'Вывоз мусора\', null, null, true, false),
  (now(), 6, 0, \'junk_removal\', \'kz\', \'Вывоз строительного мусора\', null, null, true, false),
  (now(), 7, 0, \'junk_removal\', \'kz\', \'Вывоз органического мусора\', null, null, true, false)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
    }
}
