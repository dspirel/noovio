<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250805084117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_post (id SERIAL NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, status BOOLEAN DEFAULT NULL, posted BOOLEAN NOT NULL, post_text VARCHAR(2500) DEFAULT NULL, media_urls JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2939F8F67E3C61F9 ON task_post (owner_id)');
        $this->addSql('CREATE TABLE task_post_task_schedule (task_post_id INT NOT NULL, task_schedule_id INT NOT NULL, PRIMARY KEY(task_post_id, task_schedule_id))');
        $this->addSql('CREATE INDEX IDX_10BA9BB1546829C6 ON task_post_task_schedule (task_post_id)');
        $this->addSql('CREATE INDEX IDX_10BA9BB1E27E6FE7 ON task_post_task_schedule (task_schedule_id)');
        $this->addSql('CREATE TABLE task_schedule (id SERIAL NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, next_run_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, repeat_every VARCHAR(255) NOT NULL, target_platform VARCHAR(255) NOT NULL, facebook_page VARCHAR(255) DEFAULT NULL, instagram_page VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1AF8FBB77E3C61F9 ON task_schedule (owner_id)');
        $this->addSql('COMMENT ON COLUMN task_schedule.next_run_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN task_schedule.repeat_every IS \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE task_post ADD CONSTRAINT FK_2939F8F67E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_post_task_schedule ADD CONSTRAINT FK_10BA9BB1546829C6 FOREIGN KEY (task_post_id) REFERENCES task_post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_post_task_schedule ADD CONSTRAINT FK_10BA9BB1E27E6FE7 FOREIGN KEY (task_schedule_id) REFERENCES task_schedule (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_schedule ADD CONSTRAINT FK_1AF8FBB77E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task_post DROP CONSTRAINT FK_2939F8F67E3C61F9');
        $this->addSql('ALTER TABLE task_post_task_schedule DROP CONSTRAINT FK_10BA9BB1546829C6');
        $this->addSql('ALTER TABLE task_post_task_schedule DROP CONSTRAINT FK_10BA9BB1E27E6FE7');
        $this->addSql('ALTER TABLE task_schedule DROP CONSTRAINT FK_1AF8FBB77E3C61F9');
        $this->addSql('DROP TABLE task_post');
        $this->addSql('DROP TABLE task_post_task_schedule');
        $this->addSql('DROP TABLE task_schedule');
    }
}
