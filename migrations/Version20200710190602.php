<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710190602 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main_picture DROP FOREIGN KEY FK_E290E660B281BE2E');
        $this->addSql('DROP INDEX IDX_E290E660B281BE2E ON main_picture');
        $this->addSql('ALTER TABLE main_picture DROP trick_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main_picture ADD trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE main_picture ADD CONSTRAINT FK_E290E660B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E290E660B281BE2E ON main_picture (trick_id)');
    }
}
