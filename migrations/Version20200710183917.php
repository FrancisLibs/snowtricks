<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710183917 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main_picture ADD trick_id INT NOT NULL, ADD filename VARCHAR(255) NOT NULL, DROP file_name');
        $this->addSql('ALTER TABLE main_picture ADD CONSTRAINT FK_E290E660B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_E290E660B281BE2E ON main_picture (trick_id)');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91ED6BDC9DC');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91ED6BDC9DC FOREIGN KEY (main_picture_id) REFERENCES main_picture (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main_picture DROP FOREIGN KEY FK_E290E660B281BE2E');
        $this->addSql('DROP INDEX IDX_E290E660B281BE2E ON main_picture');
        $this->addSql('ALTER TABLE main_picture ADD file_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP trick_id, DROP filename');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91ED6BDC9DC');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91ED6BDC9DC FOREIGN KEY (main_picture_id) REFERENCES picture (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
