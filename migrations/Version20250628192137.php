<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250628192137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_version_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) DEFAULT NULL, unit VARCHAR(50) DEFAULT NULL, order_number INTEGER NOT NULL, CONSTRAINT FK_6BAF78702C2A166 FOREIGN KEY (recipe_version_id) REFERENCES recipe_version (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6BAF78702C2A166 ON ingredient (recipe_version_id)');
        $this->addSql('CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_DA88B1377E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DA88B1377E3C61F9 ON recipe (owner_id)');
        $this->addSql('CREATE TABLE recipe_version (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , version_type VARCHAR(255) NOT NULL, recipe_name VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, url VARCHAR(500) DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, book_title VARCHAR(255) DEFAULT NULL, page_number VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_F3F8DD8359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F3F8DD8359D8A214 ON recipe_version (recipe_id)');
        $this->addSql('CREATE TABLE step (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_version_id INTEGER NOT NULL, description CLOB NOT NULL, order_number INTEGER NOT NULL, CONSTRAINT FK_43B9FE3C2C2A166 FOREIGN KEY (recipe_version_id) REFERENCES recipe_version (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_43B9FE3C2C2A166 ON step (recipe_version_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_version');
        $this->addSql('DROP TABLE step');
    }
}
