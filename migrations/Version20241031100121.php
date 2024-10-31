<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031100121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__questions AS SELECT id, question, reponse, nombre FROM questions');
        $this->addSql('DROP TABLE questions');
        $this->addSql('CREATE TABLE questions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question CLOB DEFAULT NULL, reponse VARCHAR(255) DEFAULT NULL, nombre CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO questions (id, question, reponse, nombre) SELECT id, question, reponse, nombre FROM __temp__questions');
        $this->addSql('DROP TABLE __temp__questions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__questions AS SELECT id, question, reponse, nombre FROM questions');
        $this->addSql('DROP TABLE questions');
        $this->addSql('CREATE TABLE questions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, question CLOB NOT NULL, reponse VARCHAR(255) NOT NULL, nombre INTEGER NOT NULL)');
        $this->addSql('INSERT INTO questions (id, question, reponse, nombre) SELECT id, question, reponse, nombre FROM __temp__questions');
        $this->addSql('DROP TABLE __temp__questions');
    }
}
