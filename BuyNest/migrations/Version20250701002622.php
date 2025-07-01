<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701002622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE city (id SERIAL NOT NULL, federal_unit_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2D5B02349CA8CE00 ON city (federal_unit_id_id)');
        $this->addSql('COMMENT ON COLUMN city.last_modified IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE country (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, acronym VARCHAR(15) DEFAULT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN country.last_modified IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE federal_unit (id SERIAL NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, acronym VARCHAR(15) DEFAULT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8FFC0D4F92F3E70 ON federal_unit (country_id)');
        $this->addSql('COMMENT ON COLUMN federal_unit.last_modified IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE inventory (id SERIAL NOT NULL, product_id INT DEFAULT NULL, store_id INT DEFAULT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B12D4A364584665A ON inventory (product_id)');
        $this->addSql('CREATE INDEX IDX_B12D4A36B092A811 ON inventory (store_id)');
        $this->addSql('COMMENT ON COLUMN inventory.last_modified IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE item_on_cart (id SERIAL NOT NULL, cart_id_id INT DEFAULT NULL, product_id_id INT DEFAULT NULL, quantity INT NOT NULL, price_at_time DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4995A4BD20AEF35F ON item_on_cart (cart_id_id)');
        $this->addSql('CREATE INDEX IDX_4995A4BDDE18E50B ON item_on_cart (product_id_id)');
        $this->addSql('COMMENT ON COLUMN item_on_cart.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, supplier_id INT DEFAULT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, active BOOLEAN NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04AD2ADD6D8C ON product (supplier_id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('COMMENT ON COLUMN product.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN product.last_modified IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE shopping_cart (id SERIAL NOT NULL, user_id_id INT NOT NULL, session_id VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72AAD4F69D86650F ON shopping_cart (user_id_id)');
        $this->addSql('COMMENT ON COLUMN shopping_cart.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN shopping_cart.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE store (id SERIAL NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, cpf_cnpj VARCHAR(20) NOT NULL, phone VARCHAR(15) DEFAULT NULL, postal_code VARCHAR(10) DEFAULT NULL, address TEXT DEFAULT NULL, address_number INT DEFAULT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF5758778BAC62AF ON store (city_id)');
        $this->addSql('COMMENT ON COLUMN store.last_modified IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE supplier (id SERIAL NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, cpf_cnpj VARCHAR(20) NOT NULL, phone VARCHAR(15) DEFAULT NULL, postal_code VARCHAR(10) DEFAULT NULL, address TEXT NOT NULL, address_number INT DEFAULT NULL, active BOOLEAN DEFAULT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B2A6C7E8BAC62AF ON supplier (city_id)');
        $this->addSql('COMMENT ON COLUMN supplier.last_modified IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B02349CA8CE00 FOREIGN KEY (federal_unit_id_id) REFERENCES federal_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE federal_unit ADD CONSTRAINT FK_A8FFC0D4F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A364584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36B092A811 FOREIGN KEY (store_id) REFERENCES store (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_on_cart ADD CONSTRAINT FK_4995A4BD20AEF35F FOREIGN KEY (cart_id_id) REFERENCES shopping_cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_on_cart ADD CONSTRAINT FK_4995A4BDDE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shopping_cart ADD CONSTRAINT FK_72AAD4F69D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF5758778BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE supplier ADD CONSTRAINT FK_9B2A6C7E8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE city DROP CONSTRAINT FK_2D5B02349CA8CE00');
        $this->addSql('ALTER TABLE federal_unit DROP CONSTRAINT FK_A8FFC0D4F92F3E70');
        $this->addSql('ALTER TABLE inventory DROP CONSTRAINT FK_B12D4A364584665A');
        $this->addSql('ALTER TABLE inventory DROP CONSTRAINT FK_B12D4A36B092A811');
        $this->addSql('ALTER TABLE item_on_cart DROP CONSTRAINT FK_4995A4BD20AEF35F');
        $this->addSql('ALTER TABLE item_on_cart DROP CONSTRAINT FK_4995A4BDDE18E50B');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD2ADD6D8C');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE shopping_cart DROP CONSTRAINT FK_72AAD4F69D86650F');
        $this->addSql('ALTER TABLE store DROP CONSTRAINT FK_FF5758778BAC62AF');
        $this->addSql('ALTER TABLE supplier DROP CONSTRAINT FK_9B2A6C7E8BAC62AF');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE federal_unit');
        $this->addSql('DROP TABLE inventory');
        $this->addSql('DROP TABLE item_on_cart');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE shopping_cart');
        $this->addSql('DROP TABLE store');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
