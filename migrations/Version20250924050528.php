<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250924050528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cooking_sessions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, cooked_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , notes CLOB DEFAULT NULL, CONSTRAINT FK_F3C920F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F3C920F59D8A214 ON cooking_sessions (recipe_id)');
        $this->addSql('CREATE TABLE dishes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, daily_meal_friendly BOOLEAN NOT NULL, prep_friendly BOOLEAN NOT NULL, freezer_friendly BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE food_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, dish_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, CONSTRAINT FK_107F2CA7148EB0CB FOREIGN KEY (dish_id) REFERENCES dishes (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_107F2CA7148EB0CB ON food_items (dish_id)');
        $this->addSql('CREATE TABLE ingredients (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, food_item_id INTEGER NOT NULL, recipe_id INTEGER NOT NULL, amount DOUBLE PRECISION NOT NULL, unit VARCHAR(10) NOT NULL, CONSTRAINT FK_4B60114F5DF08E66 FOREIGN KEY (food_item_id) REFERENCES food_items (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4B60114F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4B60114F5DF08E66 ON ingredients (food_item_id)');
        $this->addSql('CREATE INDEX IDX_4B60114F59D8A214 ON ingredients (recipe_id)');
        $this->addSql('CREATE TABLE recipe_links (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, dish_id INTEGER NOT NULL, url VARCHAR(500) NOT NULL, name VARCHAR(255) NOT NULL, author_name VARCHAR(255) NOT NULL, rating INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_C7917507148EB0CB FOREIGN KEY (dish_id) REFERENCES dishes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C7917507148EB0CB ON recipe_links (dish_id)');
        $this->addSql('CREATE TABLE recipes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, dish_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, steps CLOB NOT NULL --(DC2Type:json)
        , rating INTEGER DEFAULT NULL, notes CLOB DEFAULT NULL, CONSTRAINT FK_A369E2B5148EB0CB FOREIGN KEY (dish_id) REFERENCES dishes (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A369E2B5148EB0CB ON recipes (dish_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cooking_sessions');
        $this->addSql('DROP TABLE dishes');
        $this->addSql('DROP TABLE food_items');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('DROP TABLE recipe_links');
        $this->addSql('DROP TABLE recipes');
    }
}
