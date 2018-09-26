<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180926145544 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE item_message_media (id SERIAL NOT NULL, media_id INT DEFAULT NULL, message_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7926F63CEA9FDD75 ON item_message_media (media_id)');
        $this->addSql('CREATE INDEX IDX_7926F63C537A1329 ON item_message_media (message_id)');
        $this->addSql('CREATE UNIQUE INDEX unq_item_message_media ON item_message_media (media_id, message_id)');
        $this->addSql('CREATE TABLE item_messages (id SERIAL NOT NULL, user_id INT NOT NULL, item_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B70FD65DA76ED395 ON item_messages (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B70FD65D126F525E ON item_messages (item_id)');
        $this->addSql('ALTER TABLE item_message_media ADD CONSTRAINT FK_7926F63CEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_message_media ADD CONSTRAINT FK_7926F63C537A1329 FOREIGN KEY (message_id) REFERENCES item_messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_messages ADD CONSTRAINT FK_B70FD65DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_messages ADD CONSTRAINT FK_B70FD65D126F525E FOREIGN KEY (item_id) REFERENCES order_items (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE item_message_media DROP CONSTRAINT FK_7926F63C537A1329');
        $this->addSql('DROP TABLE item_message_media');
        $this->addSql('DROP TABLE item_messages');
    }
}
