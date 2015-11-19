<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151119180922 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE shared_order');
        $this->addSql('ALTER TABLE product ADD description2 LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE event_order CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event_order ADD CONSTRAINT FK_B43222B46B3CA4B FOREIGN KEY (id_user) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_B43222B46B3CA4B ON event_order (id_user)');
        $this->addSql('ALTER TABLE event CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76B3CA4B FOREIGN KEY (id_user) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA76B3CA4B ON event (id_user)');
        $this->addSql('ALTER TABLE order_item ADD id_product_id INT DEFAULT NULL, DROP id_product, CHANGE id_event_order id_event_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09E00EE68D FOREIGN KEY (id_product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09AC20EF43 FOREIGN KEY (id_event_order) REFERENCES event_order (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09E00EE68D ON order_item (id_product_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09AC20EF43 ON order_item (id_event_order)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shared_order (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_order_item INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76B3CA4B');
        $this->addSql('DROP INDEX IDX_3BAE0AA76B3CA4B ON event');
        $this->addSql('ALTER TABLE event CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE event_order DROP FOREIGN KEY FK_B43222B46B3CA4B');
        $this->addSql('DROP INDEX IDX_B43222B46B3CA4B ON event_order');
        $this->addSql('ALTER TABLE event_order CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09E00EE68D');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09AC20EF43');
        $this->addSql('DROP INDEX IDX_52EA1F09E00EE68D ON order_item');
        $this->addSql('DROP INDEX IDX_52EA1F09AC20EF43 ON order_item');
        $this->addSql('ALTER TABLE order_item ADD id_product INT NOT NULL, DROP id_product_id, CHANGE id_event_order id_event_order INT NOT NULL');
        $this->addSql('ALTER TABLE product DROP description2');
    }
}
