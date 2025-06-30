<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625174358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE item_on_cart (id SERIAL NOT NULL, cart_id_id INT DEFAULT NULL, product_id_id INT DEFAULT NULL, quantity INT NOT NULL, price_at_time DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4995A4BD20AEF35F ON item_on_cart (cart_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4995A4BDDE18E50B ON item_on_cart (product_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN item_on_cart.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shopping_cart (id SERIAL NOT NULL, user_id_id INT NOT NULL, session_id VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_72AAD4F69D86650F ON shopping_cart (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_cart.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN shopping_cart.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item_on_cart ADD CONSTRAINT FK_4995A4BD20AEF35F FOREIGN KEY (cart_id_id) REFERENCES shopping_cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item_on_cart ADD CONSTRAINT FK_4995A4BDDE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_cart ADD CONSTRAINT FK_72AAD4F69D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item_on_cart DROP CONSTRAINT FK_4995A4BD20AEF35F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item_on_cart DROP CONSTRAINT FK_4995A4BDDE18E50B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shopping_cart DROP CONSTRAINT FK_72AAD4F69D86650F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE item_on_cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shopping_cart
        SQL);
    }
}
