<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024045713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, link_post_id INT DEFAULT NULL, link_comment_id INT DEFAULT NULL, link_chapter_id INT DEFAULT NULL, link_user_id INT DEFAULT NULL, number INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_BF5476CA36EF59D9 (link_post_id), INDEX IDX_BF5476CAAFE3BDE6 (link_comment_id), INDEX IDX_BF5476CA15879D (link_chapter_id), INDEX IDX_BF5476CADA088960 (link_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA36EF59D9 FOREIGN KEY (link_post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAAFE3BDE6 FOREIGN KEY (link_comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA15879D FOREIGN KEY (link_chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CADA088960 FOREIGN KEY (link_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notification');
    }
}
