<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614091832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__Connection AS SELECT id, fromLocationId, toLocationId, direction FROM Connection');
        $this->addSql('DROP TABLE Connection');
        $this->addSql('CREATE TABLE Connection (id INTEGER NOT NULL, fromLocationId INTEGER NOT NULL, toLocationId INTEGER NOT NULL, direction VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO Connection (id, fromLocationId, toLocationId, direction) SELECT id, fromLocationId, toLocationId, direction FROM __temp__Connection');
        $this->addSql('DROP TABLE __temp__Connection');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__Connection AS SELECT id, fromLocationId, toLocationId, direction FROM Connection');
        $this->addSql('DROP TABLE Connection');
        $this->addSql('CREATE TABLE Connection (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fromLocationId INTEGER NOT NULL, toLocationId INTEGER NOT NULL, direction VARCHAR(10) NOT NULL)');
        $this->addSql('INSERT INTO Connection (id, fromLocationId, toLocationId, direction) SELECT id, fromLocationId, toLocationId, direction FROM __temp__Connection');
        $this->addSql('DROP TABLE __temp__Connection');
    }
}
