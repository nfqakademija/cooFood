<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151121001836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09AC20EF43');
        $this->addSql('DROP TABLE event_order');
        $this->addSql('DROP INDEX IDX_52EA1F09AC20EF43 ON order_item');
        $this->addSql('ALTER TABLE order_item CHANGE id_event_order id_user_event INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F097CE67128 FOREIGN KEY (id_user_event) REFERENCES UserEvent (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F097CE67128 ON order_item (id_user_event)');
        $this->addSql('ALTER TABLE UserEvent CHANGE id_user id_user INT DEFAULT NULL, CHANGE id_event id_event INT DEFAULT NULL');
        $this->addSql('ALTER TABLE UserEvent ADD CONSTRAINT FK_5F3A9BD26B3CA4B FOREIGN KEY (id_user) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE UserEvent ADD CONSTRAINT FK_5F3A9BD2D52B4B97 FOREIGN KEY (id_event) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_5F3A9BD26B3CA4B ON UserEvent (id_user)');
        $this->addSql('CREATE INDEX IDX_5F3A9BD2D52B4B97 ON UserEvent (id_event)');
        $this->addSql('ALTER TABLE event CHANGE id_supplier id_supplier INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7CBF180EB FOREIGN KEY (id_supplier) REFERENCES supplier (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7CBF180EB ON event (id_supplier)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event_order (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, id_event INT NOT NULL, INDEX IDX_B43222B46B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_order ADD CONSTRAINT FK_B43222B46B3CA4B FOREIGN KEY (id_user) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE UserEvent DROP FOREIGN KEY FK_5F3A9BD26B3CA4B');
        $this->addSql('ALTER TABLE UserEvent DROP FOREIGN KEY FK_5F3A9BD2D52B4B97');
        $this->addSql('DROP INDEX IDX_5F3A9BD26B3CA4B ON UserEvent');
        $this->addSql('DROP INDEX IDX_5F3A9BD2D52B4B97 ON UserEvent');
        $this->addSql('ALTER TABLE UserEvent CHANGE id_user id_user INT NOT NULL, CHANGE id_event id_event INT NOT NULL');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7CBF180EB');
        $this->addSql('DROP INDEX IDX_3BAE0AA7CBF180EB ON event');
        $this->addSql('ALTER TABLE event CHANGE id_supplier id_supplier INT NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F097CE67128');
        $this->addSql('DROP INDEX IDX_52EA1F097CE67128 ON order_item');
        $this->addSql('ALTER TABLE order_item CHANGE id_user_event id_event_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09AC20EF43 FOREIGN KEY (id_event_order) REFERENCES event_order (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09AC20EF43 ON order_item (id_event_order)');
    }
}
