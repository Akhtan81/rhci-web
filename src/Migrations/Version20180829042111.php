<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829042111 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token) values (\'admin\', \'Admin\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), true, \'12345\')');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token) values (\'partner\', \'Partner\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false, \'67890\')');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin, access_token) values (\'user\', \'User\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false, \'45628\')');

        $this->addSql("INSERT INTO partners (user_id, created_at, country_id) VALUES ((SELECT id FROM users WHERE email ='partner'), NOW(), (SELECT id FROM geo_countries WHERE name ='USA'))");
    }

    public function down(Schema $schema): void
    {

    }
}
