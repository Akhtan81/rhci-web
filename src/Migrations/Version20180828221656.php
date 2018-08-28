<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180828221656 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE credit_cards (id SERIAL NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, holder TEXT NOT NULL, code VARCHAR(16) NOT NULL, cvc VARCHAR(3) NOT NULL, month VARCHAR(2) NOT NULL, year VARCHAR(4) NOT NULL, confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_confirmed BOOLEAN NOT NULL, is_expired BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5CADD653A76ED395 ON credit_cards (user_id)');
        $this->addSql('CREATE TABLE geo_regions (id SERIAL NOT NULL, country_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, full_name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1571F4F15E237E06 ON geo_regions (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1571F4F1DBC463C4 ON geo_regions (full_name)');
        $this->addSql('CREATE INDEX IDX_1571F4F1F92F3E70 ON geo_regions (country_id)');
        $this->addSql('CREATE TABLE geo_cities (id SERIAL NOT NULL, region_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, full_name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_728C88155E237E06 ON geo_cities (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_728C8815DBC463C4 ON geo_cities (full_name)');
        $this->addSql('CREATE INDEX IDX_728C881598260155 ON geo_cities (region_id)');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, password VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, avatar TEXT DEFAULT NULL, is_active BOOLEAN NOT NULL, is_admin BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9444F97DD ON users (phone)');
        $this->addSql('CREATE TABLE categories (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('CREATE TABLE orders (id SERIAL NOT NULL, user_id INT NOT NULL, partner_id INT DEFAULT NULL, category_id INT DEFAULT NULL, partner_category_id INT DEFAULT NULL, district_id INT DEFAULT NULL, updated_by_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(16) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_scheduled_approved BOOLEAN NOT NULL, price INT NOT NULL, quantity INT NOT NULL, location_lng DOUBLE PRECISION NOT NULL, location_lat DOUBLE PRECISION NOT NULL, repeatable VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE9393F8FE ON orders (partner_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE12469DE2 ON orders (category_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE5B352BAC ON orders (partner_category_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEB08FA272 ON orders (district_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE896DBBDE ON orders (updated_by_id)');
        $this->addSql('CREATE TABLE messages (id SERIAL NOT NULL, user_id INT DEFAULT NULL, order_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB021E96A76ED395 ON messages (user_id)');
        $this->addSql('CREATE INDEX IDX_DB021E968D9F6D38 ON messages (order_id)');
        $this->addSql('CREATE TABLE media (id SERIAL NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name TEXT NOT NULL, mime_type VARCHAR(255) NOT NULL, size BIGINT NOT NULL, url TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A2CA10CA76ED395 ON media (user_id)');
        $this->addSql('CREATE TABLE message_media (id SERIAL NOT NULL, media_id INT DEFAULT NULL, message_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_958A5EE7EA9FDD75 ON message_media (media_id)');
        $this->addSql('CREATE INDEX IDX_958A5EE7537A1329 ON message_media (message_id)');
        $this->addSql('CREATE UNIQUE INDEX unq_message_media ON message_media (media_id, message_id)');
        $this->addSql('CREATE TABLE geo_districts (id SERIAL NOT NULL, city_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, full_name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C9DC32D55E237E06 ON geo_districts (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C9DC32D5DBC463C4 ON geo_districts (full_name)');
        $this->addSql('CREATE INDEX IDX_C9DC32D58BAC62AF ON geo_districts (city_id)');
        $this->addSql('CREATE TABLE payments (id SERIAL NOT NULL, order_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(16) NOT NULL, price INT NOT NULL, status VARCHAR(16) NOT NULL, provider VARCHAR(32) NOT NULL, provider_id VARCHAR(255) DEFAULT NULL, provider_response VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65D29B328D9F6D38 ON payments (order_id)');
        $this->addSql('CREATE TABLE partners (id SERIAL NOT NULL, user_id INT NOT NULL, district_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFEB5164A76ED395 ON partners (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFEB5164B08FA272 ON partners (district_id)');
        $this->addSql('CREATE TABLE geo_countries (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FC59C1A45E237E06 ON geo_countries (name)');
        $this->addSql('CREATE TABLE partner_categories (id SERIAL NOT NULL, partner_id INT NOT NULL, category_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2002458E9393F8FE ON partner_categories (partner_id)');
        $this->addSql('CREATE INDEX IDX_2002458E12469DE2 ON partner_categories (category_id)');
        $this->addSql('ALTER TABLE credit_cards ADD CONSTRAINT FK_5CADD653A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE geo_regions ADD CONSTRAINT FK_1571F4F1F92F3E70 FOREIGN KEY (country_id) REFERENCES geo_countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE geo_cities ADD CONSTRAINT FK_728C881598260155 FOREIGN KEY (region_id) REFERENCES geo_regions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE5B352BAC FOREIGN KEY (partner_category_id) REFERENCES partner_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEB08FA272 FOREIGN KEY (district_id) REFERENCES geo_districts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E968D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_media ADD CONSTRAINT FK_958A5EE7EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_media ADD CONSTRAINT FK_958A5EE7537A1329 FOREIGN KEY (message_id) REFERENCES messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE geo_districts ADD CONSTRAINT FK_C9DC32D58BAC62AF FOREIGN KEY (city_id) REFERENCES geo_cities (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partners ADD CONSTRAINT FK_EFEB5164A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partners ADD CONSTRAINT FK_EFEB5164B08FA272 FOREIGN KEY (district_id) REFERENCES geo_districts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partner_categories ADD CONSTRAINT FK_2002458E9393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partner_categories ADD CONSTRAINT FK_2002458E12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE geo_cities DROP CONSTRAINT FK_728C881598260155');
        $this->addSql('ALTER TABLE geo_districts DROP CONSTRAINT FK_C9DC32D58BAC62AF');
        $this->addSql('ALTER TABLE credit_cards DROP CONSTRAINT FK_5CADD653A76ED395');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE896DBBDE');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E96A76ED395');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CA76ED395');
        $this->addSql('ALTER TABLE partners DROP CONSTRAINT FK_EFEB5164A76ED395');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE12469DE2');
        $this->addSql('ALTER TABLE partner_categories DROP CONSTRAINT FK_2002458E12469DE2');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E968D9F6D38');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B328D9F6D38');
        $this->addSql('ALTER TABLE message_media DROP CONSTRAINT FK_958A5EE7537A1329');
        $this->addSql('ALTER TABLE message_media DROP CONSTRAINT FK_958A5EE7EA9FDD75');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEB08FA272');
        $this->addSql('ALTER TABLE partners DROP CONSTRAINT FK_EFEB5164B08FA272');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE9393F8FE');
        $this->addSql('ALTER TABLE partner_categories DROP CONSTRAINT FK_2002458E9393F8FE');
        $this->addSql('ALTER TABLE geo_regions DROP CONSTRAINT FK_1571F4F1F92F3E70');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE5B352BAC');
        $this->addSql('DROP TABLE credit_cards');
        $this->addSql('DROP TABLE geo_regions');
        $this->addSql('DROP TABLE geo_cities');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE message_media');
        $this->addSql('DROP TABLE geo_districts');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE partners');
        $this->addSql('DROP TABLE geo_countries');
        $this->addSql('DROP TABLE partner_categories');
    }
}
