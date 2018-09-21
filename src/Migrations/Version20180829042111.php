<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\CategoryType;
use App\Entity\PartnerStatus;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829042111 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO media (created_at, hash, name, mime_type, \"size\", url, type) VALUES (now(), '12345', 'Avatar', 'image/jpg', 1024, 'http://185.227.110.171:13000/img/favicon/apple-touch-icon-144x144.png', 'image')");

        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token, avatar_id, token_expires_at) values (\'admin\', \'Admin\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), true, \'12345\', (SELECT id FROM media WHERE name= \'Avatar\' LIMIT 1), now())');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token, avatar_id, token_expires_at) values (\'partner\', \'Partner\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false, \'67890\', (SELECT id FROM media WHERE name= \'Avatar\' LIMIT 1), now())');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token, avatar_id, token_expires_at) values (\'user\', \'User\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false, \'45628\', (SELECT id FROM media WHERE name= \'Avatar\' LIMIT 1), now())');

        $this->addSql("INSERT INTO locations (created_at, postal_code, address, lng, lat) VALUES (now(), '00000', 'Test address', 19.9, 99.5031)");
        $this->addSql("INSERT INTO partners (user_id, created_at, country_id, provider, location_id, status) VALUES ((SELECT id FROM users WHERE email = 'partner'), NOW(), (SELECT id FROM geo_countries WHERE name ='USA'), 'stripe', (SELECT id FROM locations WHERE postal_code = '00000' LIMIT 1), '" . PartnerStatus::APPROVED . "')");

        $this->addSql("insert into partner_postal_codes (partner_id, created_at, postal_code, order_type) values
     ((SELECT id FROM partners LIMIT 1), now(), '00001', '" . CategoryType::JUNK_REMOVAL . "'),
     ((SELECT id FROM partners LIMIT 1), now(), '00002', '" . CategoryType::JUNK_REMOVAL . "'),
     ((SELECT id FROM partners LIMIT 1), now(), '00003', '" . CategoryType::JUNK_REMOVAL . "'),
     ((SELECT id FROM partners LIMIT 1), now(), '00004', '" . CategoryType::JUNK_REMOVAL . "'),
     ((SELECT id FROM partners LIMIT 1), now(), '00001', '" . CategoryType::RECYCLING . "'),
     ((SELECT id FROM partners LIMIT 1), now(), '00002', '" . CategoryType::RECYCLING . "'),
     ((SELECT id FROM partners LIMIT 1), now(), '00003', '" . CategoryType::RECYCLING . "'),
     ((SELECT id FROM partners LIMIT 1), now(), '00004', '" . CategoryType::RECYCLING . "')");

        $this->addSql("INSERT INTO credit_cards (user_id, created_at, provider, token, name) VALUES ((SELECT id FROM users WHERE email = 'user' LIMIT 1), now(), 'stripe', '12345', '4242')");
        $this->addSql("update users set primary_credit_card_id = (select id FROM credit_cards WHERE name = '4242') where email = 'user'");
    }

    public function down(Schema $schema): void
    {

    }
}
