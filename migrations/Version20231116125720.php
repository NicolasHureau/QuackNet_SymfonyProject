<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116125720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tag_quack');
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, content, created_at, author_id, img FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quack_id INTEGER DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, img VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_83D44F6FD3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO quack (id, content, created_at, author_id, img) SELECT id, content, created_at, author_id, img FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
        $this->addSql('CREATE INDEX IDX_83D44F6FD3950CA9 ON quack (quack_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag_quack (tag_id INTEGER NOT NULL, quack_id INTEGER NOT NULL, PRIMARY KEY(tag_id, quack_id), CONSTRAINT FK_A1385669BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A1385669D3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A1385669D3950CA9 ON tag_quack (quack_id)');
        $this->addSql('CREATE INDEX IDX_A1385669BAD26311 ON tag_quack (tag_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, content, created_at, author_id, img FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, img VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO quack (id, content, created_at, author_id, img) SELECT id, content, created_at, author_id, img FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
    }
}
