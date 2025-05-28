<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528124613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE supplier (id SERIAL NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, cpf_cnpj VARCHAR(14) NOT NULL, phone VARCHAR(11) DEFAULT NULL, postal_code VARCHAR(8) DEFAULT NULL, address TEXT NOT NULL, address_number INT DEFAULT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9B2A6C7E8BAC62AF ON supplier (city_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN supplier.last_modified IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE supplier ADD CONSTRAINT FK_9B2A6C7E8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE
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
            ALTER TABLE federal_unit DROP CONSTRAINT FK_A8FFC0D4D8A48BBD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A8FFC0D4D8A48BBD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE federal_unit RENAME COLUMN country_id TO country_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE federal_unit ADD CONSTRAINT FK_A8FFC0D4D8A48BBD FOREIGN KEY (country_id_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A8FFC0D4D8A48BBD ON federal_unit (country_id_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE supplier DROP CONSTRAINT FK_9B2A6C7E8BAC62AF
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE supplier
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE federal_unit DROP CONSTRAINT fk_a8ffc0d4d8a48bbd
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_a8ffc0d4d8a48bbd
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE federal_unit RENAME COLUMN country_id_id TO country_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE federal_unit ADD CONSTRAINT fk_a8ffc0d4d8a48bbd FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_a8ffc0d4d8a48bbd ON federal_unit (country_id)
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
    }
}
