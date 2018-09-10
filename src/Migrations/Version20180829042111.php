<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829042111 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO media (created_at, hash, name, mime_type, \"size\", url, type) VALUES (now(), '12345', 'Avatar', 'image/jpg', 1024, 'http://185.227.110.171:13000/img/favicon/apple-touch-icon-144x144.png', 'image')");

        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token, avatar_id) values (\'admin\', \'Admin\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), true, \'12345\', (SELECT id FROM media WHERE name= \'Avatar\' LIMIT 1))');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token, avatar_id) values (\'partner\', \'Partner\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false, \'67890\', (SELECT id FROM media WHERE name= \'Avatar\' LIMIT 1))');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token, avatar_id) values (\'user\', \'User\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false, \'45628\', (SELECT id FROM media WHERE name= \'Avatar\' LIMIT 1))');

        $this->addSql("INSERT INTO partners (user_id, created_at, country_id) VALUES ((SELECT id FROM users WHERE email ='partner'), NOW(), (SELECT id FROM geo_countries WHERE name ='USA'))");
    }

    public function down(Schema $schema): void
    {

    }
}
