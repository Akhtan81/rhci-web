<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180829040840 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE order_items (id SERIAL NOT NULL, order_id INT NOT NULL, partner_id INT DEFAULT NULL, category_id INT DEFAULT NULL, partner_category_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('CREATE INDEX IDX_62809DB09393F8FE ON order_items (partner_id)');
        $this->addSql('CREATE INDEX IDX_62809DB012469DE2 ON order_items (category_id)');
        $this->addSql('CREATE INDEX IDX_62809DB05B352BAC ON order_items (partner_category_id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB09393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB012469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB05B352BAC FOREIGN KEY (partner_category_id) REFERENCES partner_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT fk_e52ffdee12469de2');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT fk_e52ffdee5b352bac');
        $this->addSql('DROP INDEX idx_e52ffdee12469de2');
        $this->addSql('DROP INDEX idx_e52ffdee5b352bac');
        $this->addSql('ALTER TABLE orders DROP category_id');
        $this->addSql('ALTER TABLE orders DROP partner_category_id');
        $this->addSql('ALTER TABLE orders DROP price');
        $this->addSql('ALTER TABLE orders DROP quantity');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('ALTER TABLE orders ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD partner_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD price INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT fk_e52ffdee12469de2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT fk_e52ffdee5b352bac FOREIGN KEY (partner_category_id) REFERENCES partner_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e52ffdee12469de2 ON orders (category_id)');
        $this->addSql('CREATE INDEX idx_e52ffdee5b352bac ON orders (partner_category_id)');
    }
}
