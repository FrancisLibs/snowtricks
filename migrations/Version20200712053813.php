<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200712053813 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91ED6BDC9DC');
        $this->addSql('DROP TABLE main_picture');
        $this->addSql('DROP INDEX UNIQ_D8F0A91ED6BDC9DC ON trick');
        $this->addSql('ALTER TABLE trick ADD main_image_name VARCHAR(255) NOT NULL, DROP main_picture_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE main_picture (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE trick ADD main_picture_id INT DEFAULT NULL, DROP main_image_name');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91ED6BDC9DC FOREIGN KEY (main_picture_id) REFERENCES main_picture (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91ED6BDC9DC ON trick (main_picture_id)');
    }
}
