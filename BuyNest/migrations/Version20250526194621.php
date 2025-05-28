<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250526194621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE city (id SERIAL NOT NULL, federal_unit_id INT NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2D5B02349CA8CE00 ON city (federal_unit_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN city.last_modified IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE country (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, acronym VARCHAR(15) DEFAULT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN country.last_modified IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE federal_unit (id SERIAL NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, acronym VARCHAR(15) DEFAULT NULL, active BOOLEAN NOT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A8FFC0D4D8A48BBD ON federal_unit (country_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN federal_unit.last_modified IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city ADD CONSTRAINT FK_2D5B02349CA8CE00 FOREIGN KEY (federal_unit_id) REFERENCES federal_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE federal_unit ADD CONSTRAINT FK_A8FFC0D4D8A48BBD FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE city DROP CONSTRAINT FK_2D5B02349CA8CE00
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE federal_unit DROP CONSTRAINT FK_A8FFC0D4D8A48BBD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE city
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE country
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE federal_unit
        SQL);
    }
}
