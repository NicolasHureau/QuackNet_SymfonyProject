<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114142137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quack ADD COLUMN tag CLOB DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, content, created_at, author_id, img FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, img VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO quack (id, content, created_at, author_id, img) SELECT id, content, created_at, author_id, img FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
    }
}
