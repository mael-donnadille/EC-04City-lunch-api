<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505124140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bag (id INT AUTO_INCREMENT NOT NULL, delivery_person_id INT NOT NULL, UNIQUE INDEX UNIQ_1B226841651EF7D5 (delivery_person_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE bag_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, bag_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_4BC0A2046F5D8297 (bag_id), INDEX IDX_4BC0A2044584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE delivery_person (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, is_available TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE bag ADD CONSTRAINT FK_1B226841651EF7D5 FOREIGN KEY (delivery_person_id) REFERENCES delivery_person (id)');
        $this->addSql('ALTER TABLE bag_item ADD CONSTRAINT FK_4BC0A2046F5D8297 FOREIGN KEY (bag_id) REFERENCES bag (id)');
        $this->addSql('ALTER TABLE bag_item ADD CONSTRAINT FK_4BC0A2044584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bag DROP FOREIGN KEY FK_1B226841651EF7D5');
        $this->addSql('ALTER TABLE bag_item DROP FOREIGN KEY FK_4BC0A2046F5D8297');
        $this->addSql('ALTER TABLE bag_item DROP FOREIGN KEY FK_4BC0A2044584665A');
        $this->addSql('DROP TABLE bag');
        $this->addSql('DROP TABLE bag_item');
        $this->addSql('DROP TABLE delivery_person');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
