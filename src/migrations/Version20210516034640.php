<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516034640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, phones LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ship_order (orderid INT AUTO_INCREMENT NOT NULL, orderperson_id INT NOT NULL, items LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', shipto JSON NOT NULL, INDEX IDX_F450C04DEE431C96 (orderperson_id), PRIMARY KEY(orderid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ship_order ADD CONSTRAINT FK_F450C04DEE431C96 FOREIGN KEY (orderperson_id) REFERENCES person (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ship_order DROP FOREIGN KEY FK_F450C04DEE431C96');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE ship_order');
    }
}
