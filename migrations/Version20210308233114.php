<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210308233114 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F64099A9E3C68343');
        $this->addSql('CREATE TEMPORARY TABLE __temp__email_subscriptions AS SELECT id, email, campaigns, unique_id, active, metadata FROM email_subscriptions');
        $this->addSql('DROP TABLE email_subscriptions');
        $this->addSql('CREATE TABLE email_subscriptions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(255) NOT NULL COLLATE BINARY, unique_id VARCHAR(255) NOT NULL COLLATE BINARY, active BOOLEAN DEFAULT NULL, campaigns CLOB DEFAULT NULL --(DC2Type:array)
        , metadata CLOB DEFAULT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO email_subscriptions (id, email, campaigns, unique_id, active, metadata) SELECT id, email, campaigns, unique_id, active, metadata FROM __temp__email_subscriptions');
        $this->addSql('DROP TABLE __temp__email_subscriptions');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F64099A9E3C68343 ON email_subscriptions (unique_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F64099A9E3C68343');
        $this->addSql('CREATE TEMPORARY TABLE __temp__email_subscriptions AS SELECT id, email, campaigns, unique_id, active, metadata FROM email_subscriptions');
        $this->addSql('DROP TABLE email_subscriptions');
        $this->addSql('CREATE TABLE email_subscriptions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(255) NOT NULL, unique_id VARCHAR(255) NOT NULL, active BOOLEAN DEFAULT NULL, campaigns CLOB DEFAULT \'NULL --(DC2Type:array)\' COLLATE BINARY --(DC2Type:array)
        , metadata CLOB DEFAULT \'NULL --(DC2Type:json)\' COLLATE BINARY --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO email_subscriptions (id, email, campaigns, unique_id, active, metadata) SELECT id, email, campaigns, unique_id, active, metadata FROM __temp__email_subscriptions');
        $this->addSql('DROP TABLE __temp__email_subscriptions');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F64099A9E3C68343 ON email_subscriptions (unique_id)');
    }
}
