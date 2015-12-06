<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151125004600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE UserEvent (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, id_event INT DEFAULT NULL, paid NUMERIC(10, 2) NOT NULL, accepted_user TINYINT(1) NOT NULL, accepted_host TINYINT(1) NOT NULL, INDEX IDX_5F3A9BD26B3CA4B (id_user), INDEX IDX_5F3A9BD2D52B4B97 (id_event), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE UserEvent ADD CONSTRAINT FK_5F3A9BD26B3CA4B FOREIGN KEY (id_user) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE UserEvent ADD CONSTRAINT FK_5F3A9BD2D52B4B97 FOREIGN KEY (id_event) REFERENCES event (id)');
        $this->addSql('DROP TABLE user_event');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F097CE67128 FOREIGN KEY (id_user_event) REFERENCES UserEvent (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F097CE67128');
        $this->addSql('CREATE TABLE user_event (id INT AUTO_INCREMENT NOT NULL, id_event INT DEFAULT NULL, id_user INT DEFAULT NULL, paid NUMERIC(10, 2) NOT NULL, accepted_user TINYINT(1) NOT NULL, accepted_host TINYINT(1) NOT NULL, INDEX IDX_D96CF1FF6B3CA4B (id_user), INDEX IDX_D96CF1FFD52B4B97 (id_event), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FFD52B4B97 FOREIGN KEY (id_event) REFERENCES event (id)');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FF6B3CA4B FOREIGN KEY (id_user) REFERENCES fos_user (id)');
        $this->addSql('DROP TABLE UserEvent');
    }
}
