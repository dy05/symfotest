<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260920214557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY `FK_5A8A6C8D3DA5256D`');
        $this->addSql('DROP INDEX IDX_5A8A6C8D3DA5256D ON post');
        $this->addSql('ALTER TABLE post ADD file_path VARCHAR(255) DEFAULT NULL, DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD image_id INT DEFAULT NULL, DROP file_path');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT `FK_5A8A6C8D3DA5256D` FOREIGN KEY (image_id) REFERENCES media_object (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D3DA5256D ON post (image_id)');
    }
}
