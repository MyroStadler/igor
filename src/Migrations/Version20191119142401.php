<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create store table
 */
final class Version20191119142401 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create store table';
    }

    public function up(Schema $schema) : void
    {

        $this->addSql("
            CREATE TABLE store (
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(190) NOT NULL, 
                type VARCHAR(255) NULL, 
                value TEXT NULL, 
                UNIQUE INDEX UNIQ_20191119142401_01 (name), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE store');
    }
}
