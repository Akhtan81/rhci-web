<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200908123043 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partners ADD can_manage_busy_bee_orders BOOLEAN NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE partners ADD can_manage_moving_orders BOOLEAN NOT NULL DEFAULT TRUE');
        $this->addSql('UPDATE partners SET can_manage_busy_bee_orders = TRUE WHERE account_id IS NOT NULL');
        $this->addSql('UPDATE partners SET can_manage_moving_orders = TRUE WHERE account_id IS NOT NULL');
        $this->addSql('ALTER TABLE partners ALTER can_manage_busy_bee_orders DROP DEFAULT');
        $this->addSql('ALTER TABLE partners ALTER can_manage_moving_orders DROP DEFAULT');
        $this->addSql(
            "CREATE TABLE groups (
                id SERIAL NOT NULL, 
                codename VARCHAR(32) NOT NULL, 
                name_en VARCHAR(32) NOT NULL, 
                name_kz VARCHAR(32) NOT NULL, 
                name_ru VARCHAR(32) NOT NULL, 
                fa_icon_name VARCHAR(32) NOT NULL,
                bidirectional BOOLEAN NOT NULL DEFAULT false, 
                flag1 BOOLEAN NOT NULL DEFAULT false,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL default (now() at time zone 'utc'), 
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL default (now() at time zone 'utc'),
                deleted_at TIMESTAMP(0) WITHOUT TIME ZONE default NULL,
                PRIMARY KEY(id)
            )"
        );
        $this->addSql(
            "INSERT INTO groups (codename, name_en, name_kz, name_ru, fa_icon_name, bidirectional) VALUES 
            ('junk_removal', 'Junk Removal', 'Junk Removal' , 'Junk Removal', 'fa fa-cubes', false),
            ('recycling', 'Recycling', 'Recycling' , 'Recycling', 'fa fa-recycle', true),
            ('shredding', 'Shredding', 'Shredding' , 'Shredding', 'fa fa-stack-overflow', false),
            ('donation', 'Donation', 'Donation' , 'Donation', 'fa fa-gift', false),
            ('busybee', 'Busy Bee', 'Busy Bee' , 'Busy Bee', 'fa fa-stack-overflow', true),
            ('moving', 'Moving', 'Moving' , 'Moving', 'fa fa-stack-overflow', false)
        ");
        $this->addSql(
            "alter table partner_categories add bidirectional boolean not null default false
        ");
        $this->addSql(
            "alter table orders add price_for_user int not null default 0
        ");
        $this->addSql(
            "alter table payments add to_client boolean not null default false
        ");
        $this->addSql(
            "alter table users add account_id text default null"
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            "DROP TABLE IF EXISTS groups"
        );
        $this->addSql('ALTER TABLE partners DROP column if exists can_manage_busy_bee_orders');
        $this->addSql('ALTER TABLE partners DROP column if exists can_manage_moving_orders');
        $this->addSql('ALTER TABLE partner_categories DROP column if exists bidirectional');
        $this->addSql('ALTER TABLE orders drop column if exists price_for_user');
        $this->addSql('ALTER TABLE payments drop column if exists to_client');
        $this->addSql('ALTER TABLE users drop column if exists account_id');
    }
}
