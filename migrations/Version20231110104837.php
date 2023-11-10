<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231110104837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE duck (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, duckname VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_538A954790361416 ON duck (duckname)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_538A9547E7927C74 ON duck (email)');
        $this->addSql('DROP TABLE ducks');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ducks (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, duckname VARCHAR(180) NOT NULL COLLATE "BINARY", roles CLOB NOT NULL COLLATE "BINARY" --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE "BINARY", firstname VARCHAR(255) NOT NULL COLLATE "BINARY", lastname VARCHAR(255) NOT NULL COLLATE "BINARY", email VARCHAR(255) NOT NULL COLLATE "BINARY", is_verified BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F3E591ADE7927C74 ON ducks (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F3E591AD90361416 ON ducks (duckname)');
        $this->addSql('DROP TABLE duck');
    }
}
