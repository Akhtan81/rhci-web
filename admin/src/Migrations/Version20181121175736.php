<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181121175736 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("INSERT INTO categories (created_at, locale, type, name, lvl, price, ordering, has_price, is_selectable) VALUES
(now(), 'en', 'donation', 'Furniture', 0, null, 1, false, true),
(now(), 'en', 'donation', 'Books', 0, null, 2, false, true),
(now(), 'en', 'donation', 'Products', 0, null, 3, false, true),
(now(), 'en', 'donation', 'Clothes', 0, null, 4, false, true),
(now(), 'en', 'donation', 'Toys', 0, null, 5, false, true),
(now(), 'en', 'donation', 'Other', 0, null, 6, false, true);
");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
