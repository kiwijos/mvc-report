<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230718145023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, title, description, isbn, author, image_url FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, isbn VARCHAR(13) NOT NULL, author VARCHAR(255) NOT NULL, imageUrl VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO book (id, title, description, isbn, author, imageUrl) SELECT id, title, description, isbn, author, image_url FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BD70C0FCC1CF4E6 ON book (isbn)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__Book AS SELECT id, title, description, isbn, author, imageUrl FROM Book');
        $this->addSql('DROP TABLE Book');
        $this->addSql('CREATE TABLE Book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, isbn CHAR(13) NOT NULL, author VARCHAR(255) NOT NULL, image_url VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO Book (id, title, description, isbn, author, image_url) SELECT id, title, description, isbn, author, imageUrl FROM __temp__Book');
        $this->addSql('DROP TABLE __temp__Book');
    }
}
