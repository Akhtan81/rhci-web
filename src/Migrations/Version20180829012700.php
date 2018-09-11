<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180829012700 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE user_locations (id SERIAL NOT NULL, user_id INT DEFAULT NULL, location_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1706C75EA76ED395 ON user_locations (user_id)');
        $this->addSql('CREATE INDEX IDX_1706C75E64D218E ON user_locations (location_id)');
        $this->addSql('CREATE UNIQUE INDEX unq_user_locations ON user_locations (user_id, location_id)');
        $this->addSql('CREATE TABLE order_items (id SERIAL NOT NULL, order_id INT NOT NULL, category_id INT DEFAULT NULL, partner_category_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('CREATE INDEX IDX_62809DB012469DE2 ON order_items (category_id)');
        $this->addSql('CREATE INDEX IDX_62809DB05B352BAC ON order_items (partner_category_id)');
        $this->addSql('CREATE TABLE credit_cards (id SERIAL NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, provider VARCHAR(32) NOT NULL, token VARCHAR(255) NOT NULL, name VARCHAR(64) NOT NULL, currency VARCHAR(12) DEFAULT NULL, type TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5CADD6535F37A13B ON credit_cards (token)');
        $this->addSql('CREATE INDEX IDX_5CADD653A76ED395 ON credit_cards (user_id)');
        $this->addSql('CREATE TABLE locations (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, postal_code TEXT NOT NULL, address TEXT DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, lat DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE geo_regions (id SERIAL NOT NULL, country_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, full_name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1571F4F15E237E06 ON geo_regions (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1571F4F1DBC463C4 ON geo_regions (full_name)');
        $this->addSql('CREATE INDEX IDX_1571F4F1F92F3E70 ON geo_regions (country_id)');
        $this->addSql('CREATE TABLE geo_cities (id SERIAL NOT NULL, region_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, full_name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_728C88155E237E06 ON geo_cities (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_728C8815DBC463C4 ON geo_cities (full_name)');
        $this->addSql('CREATE INDEX IDX_728C881598260155 ON geo_cities (region_id)');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, avatar_id INT DEFAULT NULL, location_id INT DEFAULT NULL, primary_credit_card_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, password VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, is_admin BOOLEAN NOT NULL, access_token VARCHAR(128) NOT NULL, token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9444F97DD ON users (phone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9B6A2DD68 ON users (access_token)');
        $this->addSql('CREATE INDEX IDX_1483A5E986383B10 ON users (avatar_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E964D218E ON users (location_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C5F71852 ON users (primary_credit_card_id)');
        $this->addSql('CREATE TABLE categories (id SERIAL NOT NULL, parent_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, type VARCHAR(16) NOT NULL, name TEXT NOT NULL, lvl INT NOT NULL, price INT DEFAULT NULL, ordering INT NOT NULL, has_price BOOLEAN NOT NULL, is_selectable BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX unq_categories ON categories (name, parent_id, locale)');
        $this->addSql('CREATE TABLE partner_postal_codes (id SERIAL NOT NULL, partner_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, postal_code VARCHAR(16) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_33AF9CC0EA98E376 ON partner_postal_codes (postal_code)');
        $this->addSql('CREATE INDEX IDX_33AF9CC09393F8FE ON partner_postal_codes (partner_id)');
        $this->addSql('CREATE TABLE orders (id SERIAL NOT NULL, user_id INT NOT NULL, partner_id INT DEFAULT NULL, updated_by_id INT NOT NULL, location_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(16) NOT NULL, status VARCHAR(16) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_schedule_approved BOOLEAN NOT NULL, price INT NOT NULL, is_price_approved BOOLEAN NOT NULL, repeatable VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE9393F8FE ON orders (partner_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE896DBBDE ON orders (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE64D218E ON orders (location_id)');
        $this->addSql('CREATE TABLE messages (id SERIAL NOT NULL, user_id INT DEFAULT NULL, order_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB021E96A76ED395 ON messages (user_id)');
        $this->addSql('CREATE INDEX IDX_DB021E968D9F6D38 ON messages (order_id)');
        $this->addSql('CREATE TABLE media (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, hash VARCHAR(64) NOT NULL, name TEXT NOT NULL, mime_type VARCHAR(255) NOT NULL, size BIGINT NOT NULL, url TEXT NOT NULL, type VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10CD1B862B8 ON media (hash)');
        $this->addSql('CREATE TABLE message_media (id SERIAL NOT NULL, media_id INT DEFAULT NULL, message_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_958A5EE7EA9FDD75 ON message_media (media_id)');
        $this->addSql('CREATE INDEX IDX_958A5EE7537A1329 ON message_media (message_id)');
        $this->addSql('CREATE UNIQUE INDEX unq_message_media ON message_media (media_id, message_id)');
        $this->addSql('CREATE TABLE geo_districts (id SERIAL NOT NULL, city_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, postal_code VARCHAR(32) NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, full_name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C9DC32D5EA98E376 ON geo_districts (postal_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C9DC32D55E237E06 ON geo_districts (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C9DC32D5DBC463C4 ON geo_districts (full_name)');
        $this->addSql('CREATE INDEX IDX_C9DC32D58BAC62AF ON geo_districts (city_id)');
        $this->addSql('CREATE TABLE payments (id SERIAL NOT NULL, order_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(16) NOT NULL, price INT NOT NULL, status VARCHAR(16) NOT NULL, provider VARCHAR(32) NOT NULL, provider_id TEXT DEFAULT NULL, provider_response TEXT, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65D29B328D9F6D38 ON payments (order_id)');
        $this->addSql('CREATE TABLE partners (id SERIAL NOT NULL, user_id INT NOT NULL, country_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, requested_postal_codes TEXT DEFAULT NULL, provider VARCHAR(32) NOT NULL, account_id TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFEB5164A76ED395 ON partners (user_id)');
        $this->addSql('CREATE INDEX IDX_EFEB5164F92F3E70 ON partners (country_id)');
        $this->addSql('CREATE TABLE geo_countries (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locale VARCHAR(4) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FC59C1A45E237E06 ON geo_countries (name)');
        $this->addSql('CREATE TABLE partner_categories (id SERIAL NOT NULL, partner_id INT NOT NULL, category_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2002458E9393F8FE ON partner_categories (partner_id)');
        $this->addSql('CREATE INDEX IDX_2002458E12469DE2 ON partner_categories (category_id)');
        $this->addSql('CREATE UNIQUE INDEX unq_partner_categories ON partner_categories (partner_id, category_id)');
        $this->addSql('ALTER TABLE user_locations ADD CONSTRAINT FK_1706C75EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_locations ADD CONSTRAINT FK_1706C75E64D218E FOREIGN KEY (location_id) REFERENCES locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB012469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB05B352BAC FOREIGN KEY (partner_category_id) REFERENCES partner_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credit_cards ADD CONSTRAINT FK_5CADD653A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE geo_regions ADD CONSTRAINT FK_1571F4F1F92F3E70 FOREIGN KEY (country_id) REFERENCES geo_countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE geo_cities ADD CONSTRAINT FK_728C881598260155 FOREIGN KEY (region_id) REFERENCES geo_regions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E986383B10 FOREIGN KEY (avatar_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E964D218E FOREIGN KEY (location_id) REFERENCES user_locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9C5F71852 FOREIGN KEY (primary_credit_card_id) REFERENCES credit_cards (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partner_postal_codes ADD CONSTRAINT FK_33AF9CC09393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE64D218E FOREIGN KEY (location_id) REFERENCES locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E968D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_media ADD CONSTRAINT FK_958A5EE7EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_media ADD CONSTRAINT FK_958A5EE7537A1329 FOREIGN KEY (message_id) REFERENCES messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE geo_districts ADD CONSTRAINT FK_C9DC32D58BAC62AF FOREIGN KEY (city_id) REFERENCES geo_cities (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B328D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partners ADD CONSTRAINT FK_EFEB5164A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partners ADD CONSTRAINT FK_EFEB5164F92F3E70 FOREIGN KEY (country_id) REFERENCES geo_countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partner_categories ADD CONSTRAINT FK_2002458E9393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partner_categories ADD CONSTRAINT FK_2002458E12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E964D218E');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9C5F71852');
        $this->addSql('ALTER TABLE user_locations DROP CONSTRAINT FK_1706C75E64D218E');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE64D218E');
        $this->addSql('ALTER TABLE geo_cities DROP CONSTRAINT FK_728C881598260155');
        $this->addSql('ALTER TABLE geo_districts DROP CONSTRAINT FK_C9DC32D58BAC62AF');
        $this->addSql('ALTER TABLE user_locations DROP CONSTRAINT FK_1706C75EA76ED395');
        $this->addSql('ALTER TABLE credit_cards DROP CONSTRAINT FK_5CADD653A76ED395');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE896DBBDE');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E96A76ED395');
        $this->addSql('ALTER TABLE partners DROP CONSTRAINT FK_EFEB5164A76ED395');
        $this->addSql('ALTER TABLE order_items DROP CONSTRAINT FK_62809DB012469DE2');
        $this->addSql('ALTER TABLE categories DROP CONSTRAINT FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE partner_categories DROP CONSTRAINT FK_2002458E12469DE2');
        $this->addSql('ALTER TABLE order_items DROP CONSTRAINT FK_62809DB08D9F6D38');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E968D9F6D38');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B328D9F6D38');
        $this->addSql('ALTER TABLE message_media DROP CONSTRAINT FK_958A5EE7537A1329');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E986383B10');
        $this->addSql('ALTER TABLE message_media DROP CONSTRAINT FK_958A5EE7EA9FDD75');
        $this->addSql('ALTER TABLE partner_postal_codes DROP CONSTRAINT FK_33AF9CC09393F8FE');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE9393F8FE');
        $this->addSql('ALTER TABLE partner_categories DROP CONSTRAINT FK_2002458E9393F8FE');
        $this->addSql('ALTER TABLE geo_regions DROP CONSTRAINT FK_1571F4F1F92F3E70');
        $this->addSql('ALTER TABLE partners DROP CONSTRAINT FK_EFEB5164F92F3E70');
        $this->addSql('ALTER TABLE order_items DROP CONSTRAINT FK_62809DB05B352BAC');
        $this->addSql('DROP TABLE user_locations');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE credit_cards');
        $this->addSql('DROP TABLE locations');
        $this->addSql('DROP TABLE geo_regions');
        $this->addSql('DROP TABLE geo_cities');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE partner_postal_codes');
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
