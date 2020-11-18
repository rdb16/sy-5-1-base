<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201117193851 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar ADD user_calendar_id INT NOT NULL');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146A35B6B5D FOREIGN KEY (user_calendar_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6EA9A146A35B6B5D ON calendar (user_calendar_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146A35B6B5D');
        $this->addSql('DROP INDEX IDX_6EA9A146A35B6B5D ON calendar');
        $this->addSql('ALTER TABLE calendar DROP user_calendar_id');
    }
}
