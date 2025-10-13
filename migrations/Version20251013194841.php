<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013194841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ingredients AS SELECT id, food_item_id, recipe_id, amount, unit FROM ingredients');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('CREATE TABLE ingredients (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, food_item_id INTEGER DEFAULT NULL, recipe_id INTEGER NOT NULL, amount DOUBLE PRECISION NOT NULL, unit VARCHAR(10) NOT NULL, food_item_name VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_4B60114F5DF08E66 FOREIGN KEY (food_item_id) REFERENCES food_items (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4B60114F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ingredients (id, food_item_id, recipe_id, amount, unit) SELECT id, food_item_id, recipe_id, amount, unit FROM __temp__ingredients');
        $this->addSql('DROP TABLE __temp__ingredients');
        $this->addSql('CREATE INDEX IDX_4B60114F59D8A214 ON ingredients (recipe_id)');
        $this->addSql('CREATE INDEX IDX_4B60114F5DF08E66 ON ingredients (food_item_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, email, roles, password, first_name, last_name, created_at, last_login_at FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, last_login_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO users (id, email, roles, password, first_name, last_name, created_at, last_login_at) SELECT id, email, roles, password, first_name, last_name, created_at, last_login_at FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ingredients AS SELECT id, food_item_id, recipe_id, amount, unit FROM ingredients');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('CREATE TABLE ingredients (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, food_item_id INTEGER NOT NULL, recipe_id INTEGER NOT NULL, amount DOUBLE PRECISION NOT NULL, unit VARCHAR(10) NOT NULL, CONSTRAINT FK_4B60114F5DF08E66 FOREIGN KEY (food_item_id) REFERENCES food_items (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4B60114F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ingredients (id, food_item_id, recipe_id, amount, unit) SELECT id, food_item_id, recipe_id, amount, unit FROM __temp__ingredients');
        $this->addSql('DROP TABLE __temp__ingredients');
        $this->addSql('CREATE INDEX IDX_4B60114F5DF08E66 ON ingredients (food_item_id)');
        $this->addSql('CREATE INDEX IDX_4B60114F59D8A214 ON ingredients (recipe_id)');
        $this->addSql('ALTER TABLE users ADD COLUMN google_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD COLUMN avatar VARCHAR(500) DEFAULT NULL');
    }
}
