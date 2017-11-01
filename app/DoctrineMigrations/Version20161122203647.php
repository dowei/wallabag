<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Methods and properties removed from `FOS\UserBundle\Model\User`.
 *
 * - `$expired`
 * - `$credentialsExpired`
 * - `setExpired()` (use `setExpiresAt(\DateTime::now()` instead)
 * - `setCredentialsExpired()` (use `setCredentialsExpireAt(\DateTime::now()` instead)
 *
 * You need to drop the fields `expired` and `credentials_expired` from your database
 * schema, because they aren't mapped anymore.
 */
class Version20161122203647 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $userTable = $schema->getTable($this->getTable('user'));

        $this->skipIf(false === $userTable->hasColumn('expired') || false === $userTable->hasColumn('credentials_expired'), 'It seems that you already played this migration.');

        $userTable->dropColumn('expired');
        $userTable->dropColumn('credentials_expired');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $userTable = $schema->getTable($this->getTable('user'));

        $this->skipIf(true === $userTable->hasColumn('expired') || true === $userTable->hasColumn('credentials_expired'), 'It seems that you already played this migration.');

        $userTable->addColumn('expired', 'smallint', ['notnull' => false]);
        $userTable->addColumn('credentials_expired', 'smallint', ['notnull' => false]);
    }

    private function getTable($tableName)
    {
        return $this->container->getParameter('database_table_prefix') . $tableName;
    }
}
