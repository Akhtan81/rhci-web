<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180829042111 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin) values (\'admin\', \'Admin\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), true)');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin) values (\'partner\', \'Partner\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false)');
        $this->addSql('insert into users(email, name, password, is_active, created_at, is_admin) values (\'user\', \'User\', \'$2y$12$V/sFLNxZPUOZ3QowrsezlOeiYrr.J3NBw7xnQjpyFijmE89IyrrzS\', true, now(), false)');


    }

    public function down(Schema $schema) : void
    {

    }
}
