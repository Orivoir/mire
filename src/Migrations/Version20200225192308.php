<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225192308 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, create_at DATETIME NOT NULL, is_public TINYINT(1) NOT NULL, is_remove TINYINT(1) NOT NULL, remove_at DATETIME DEFAULT NULL, is_warning_public TINYINT(1) NOT NULL, INDEX IDX_23A0E66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentary (id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, send_at DATETIME NOT NULL, is_remove TINYINT(1) NOT NULL, remove_at DATETIME DEFAULT NULL, INDEX IDX_1CAC12CA7294869C (article_id), INDEX IDX_1CAC12CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, box_id INT NOT NULL, send_at DATETIME NOT NULL, content LONGTEXT NOT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_B6BD307FD8177B3F (box_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_box (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_219CC2D6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, fname VARCHAR(50) DEFAULT NULL, name VARCHAR(50) DEFAULT NULL, is_valid TINYINT(1) NOT NULL, create_at DATETIME NOT NULL, is_remove TINYINT(1) NOT NULL, remove_at DATETIME DEFAULT NULL, token VARCHAR(32) NOT NULL, is_public_email TINYINT(1) NOT NULL, is_public_profil TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CA7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD8177B3F FOREIGN KEY (box_id) REFERENCES message_box (id)');
        $this->addSql('ALTER TABLE message_box ADD CONSTRAINT FK_219CC2D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commentary DROP FOREIGN KEY FK_1CAC12CA7294869C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FD8177B3F');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A76ED395');
        $this->addSql('ALTER TABLE commentary DROP FOREIGN KEY FK_1CAC12CAA76ED395');
        $this->addSql('ALTER TABLE message_box DROP FOREIGN KEY FK_219CC2D6A76ED395');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE commentary');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_box');
        $this->addSql('DROP TABLE user');
    }
}
