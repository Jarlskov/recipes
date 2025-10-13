<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013200057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ingredients AS SELECT id, food_item_id, recipe_id, amount, unit, food_item_name FROM ingredients');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('CREATE TABLE ingredients (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, food_item_id INTEGER DEFAULT NULL, recipe_id INTEGER NOT NULL, amount DOUBLE PRECISION DEFAULT NULL, unit VARCHAR(10) DEFAULT NULL, food_item_name VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_4B60114F5DF08E66 FOREIGN KEY (food_item_id) REFERENCES food_items (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4B60114F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ingredients (id, food_item_id, recipe_id, amount, unit, food_item_name) SELECT id, food_item_id, recipe_id, amount, unit, food_item_name FROM __temp__ingredients');
        $this->addSql('DROP TABLE __temp__ingredients');
        $this->addSql('CREATE INDEX IDX_4B60114F5DF08E66 ON ingredients (food_item_id)');
        $this->addSql('CREATE INDEX IDX_4B60114F59D8A214 ON ingredients (recipe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ingredients AS SELECT id, food_item_id, recipe_id, food_item_name, amount, unit FROM ingredients');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('CREATE TABLE ingredients (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, food_item_id INTEGER DEFAULT NULL, recipe_id INTEGER NOT NULL, food_item_name VARCHAR(255) DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, unit VARCHAR(10) NOT NULL, CONSTRAINT FK_4B60114F5DF08E66 FOREIGN KEY (food_item_id) REFERENCES food_items (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4B60114F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipes (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ingredients (id, food_item_id, recipe_id, food_item_name, amount, unit) SELECT id, food_item_id, recipe_id, food_item_name, amount, unit FROM __temp__ingredients');
        $this->addSql('DROP TABLE __temp__ingredients');
        $this->addSql('CREATE INDEX IDX_4B60114F5DF08E66 ON ingredients (food_item_id)');
        $this->addSql('CREATE INDEX IDX_4B60114F59D8A214 ON ingredients (recipe_id)');
    }
}
