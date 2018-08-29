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
  (now(), 0, 0, \'junk_removal\', \'ru\', \'Мебель\', null, null, false, false),
  (now(), 1, 0, \'junk_removal\', \'ru\', \'Электроника\', null, null, false, false),
  (now(), 2, 0, \'junk_removal\', \'ru\', \'Кухня\', null, null, false, false),
  (now(), 3, 0, \'junk_removal\', \'ru\', \'Матрасы\', null, null, false, false),
  (now(), 4, 0, \'junk_removal\', \'ru\', \'Разное\', null, 3000, true, true),
  (now(), 5, 0, \'junk_removal\', \'ru\', \'Вывоз мусора\', null, null, true, false),
  (now(), 6, 0, \'junk_removal\', \'ru\', \'Вывоз строительного мусора\', null, null, true, false),
  (now(), 7, 0, \'junk_removal\', \'ru\', \'Вывоз органического мусора\', null, null, true, false)');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
    }
}
