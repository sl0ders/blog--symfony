<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211025085926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA15879D');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA36EF59D9');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAAFE3BDE6');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CADA088960');
        $this->addSql('DROP INDEX IDX_BF5476CA15879D ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CA36EF59D9 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CAAFE3BDE6 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CADA088960 ON notification');
        $this->addSql('ALTER TABLE notification ADD link VARCHAR(255) NOT NULL, DROP link_post_id, DROP link_comment_id, DROP link_chapter_id, DROP link_user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD link_post_id INT DEFAULT NULL, ADD link_comment_id INT DEFAULT NULL, ADD link_chapter_id INT DEFAULT NULL, ADD link_user_id INT DEFAULT NULL, DROP link');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA15879D FOREIGN KEY (link_chapter_id) REFERENCES chapter (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA36EF59D9 FOREIGN KEY (link_post_id) REFERENCES post (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAAFE3BDE6 FOREIGN KEY (link_comment_id) REFERENCES comment (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CADA088960 FOREIGN KEY (link_user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BF5476CA15879D ON notification (link_chapter_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA36EF59D9 ON notification (link_post_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAAFE3BDE6 ON notification (link_comment_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CADA088960 ON notification (link_user_id)');
    }
}
