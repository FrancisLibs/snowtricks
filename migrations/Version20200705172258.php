<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200705172258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD picture_id INT DEFAULT NULL, DROP profile_image');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE45BDBF ON user (picture_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_8D93D649EE45BDBF ON user');
        $this->addSql('ALTER TABLE user ADD profile_image VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_bin`, DROP picture_id');
    }
}
