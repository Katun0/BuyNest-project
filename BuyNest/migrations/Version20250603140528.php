<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603140528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE inventory (id SERIAL NOT NULL, product_id INT DEFAULT NULL, store_id INT DEFAULT NULL, quantity INT NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B12D4A364584665A ON inventory (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B12D4A36B092A811 ON inventory (store_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN inventory.last_modified IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A364584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A36B092A811 FOREIGN KEY (store_id) REFERENCES store (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city DROP CONSTRAINT FK_2D5B02349CA8CE00
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2D5B02349CA8CE00
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city RENAME COLUMN federal_unit_id TO federal_unit_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city ADD CONSTRAINT FK_2D5B02349CA8CE00 FOREIGN KEY (federal_unit_id_id) REFERENCES federal_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2D5B02349CA8CE00 ON city (federal_unit_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX idx_a8ffc0d4d8a48bbd RENAME TO IDX_A8FFC0D4F92F3E70
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inventory DROP CONSTRAINT FK_B12D4A364584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inventory DROP CONSTRAINT FK_B12D4A36B092A811
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE inventory
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city DROP CONSTRAINT fk_2d5b02349ca8ce00
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_2d5b02349ca8ce00
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city RENAME COLUMN federal_unit_id_id TO federal_unit_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city ADD CONSTRAINT fk_2d5b02349ca8ce00 FOREIGN KEY (federal_unit_id) REFERENCES federal_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_2d5b02349ca8ce00 ON city (federal_unit_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX idx_a8ffc0d4f92f3e70 RENAME TO idx_a8ffc0d4d8a48bbd
        SQL);
    }
}
