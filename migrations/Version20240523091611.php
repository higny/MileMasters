<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240523091611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entry (id INT AUTO_INCREMENT NOT NULL, tests_id INT NOT NULL, student_id INT NOT NULL, no_dos INT NOT NULL, rw TINYINT(1) NOT NULL, tstart TIME NOT NULL, tend TIME DEFAULT NULL, temps VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', INDEX IDX_2B219D70F5D80971 (tests_id), INDEX IDX_2B219D70CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70F5D80971 FOREIGN KEY (tests_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE test CHANGE tstart tstart TIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70F5D80971');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70CB944F1A');
        $this->addSql('DROP TABLE entry');
        $this->addSql('ALTER TABLE test CHANGE tstart tstart TIME NOT NULL');
    }
}
