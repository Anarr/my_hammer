<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181101190222 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE job ADD service_id_id INT NOT NULL, ADD city_id_id INT NOT NULL, DROP service_id, DROP city_id, CHANGE title title VARCHAR(255) NOT NULL, CHANGE zip_code zip_code VARCHAR(5) NOT NULL, CHANGE created_date created_date DATETIME DEFAULT NULL, CHANGE updated_date updated_date DATETIME DEFAULT NULL, CHANGE active active INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8D63673B0 FOREIGN KEY (service_id_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F83CCE3900 FOREIGN KEY (city_id_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8D63673B0 ON job (service_id_id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F83CCE3900 ON job (city_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8D63673B0');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F83CCE3900');
        $this->addSql('DROP INDEX IDX_FBD8E0F8D63673B0 ON job');
        $this->addSql('DROP INDEX IDX_FBD8E0F83CCE3900 ON job');
        $this->addSql('ALTER TABLE job ADD service_id INT NOT NULL, ADD city_id INT NOT NULL, DROP service_id_id, DROP city_id_id, CHANGE title title VARCHAR(120) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE zip_code zip_code VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE created_date created_date DATETIME NOT NULL, CHANGE updated_date updated_date DATETIME NOT NULL, CHANGE active active INT NOT NULL');
    }
}
