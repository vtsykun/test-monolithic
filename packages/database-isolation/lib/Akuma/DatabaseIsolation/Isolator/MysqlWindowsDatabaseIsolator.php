<?php

namespace Akuma\DatabaseIsolation\Isolator;

use Akuma\DatabaseIsolation\Model\DatabaseConfigurationInterface;
use Akuma\DatabaseIsolation\Service\ProcessExecutor;

class MysqlWindowsDatabaseIsolator extends AbstractDatabaseIsolator
{
    const SALT = 'isolator';

    /**
     * @var string
     */
    protected $mysqlBin = 'mysql';

    /**
     * @var string
     */
    protected $mysqlDumpBin = 'mysqldump';

    /**
     * @var ProcessExecutor
     */
    protected $processExecutor;

    /**
     * @param ProcessExecutor $processExecutor
     */
    public function __construct(ProcessExecutor $processExecutor)
    {
        $this->processExecutor = $processExecutor;
    }

    /**
     * @param string $mysqlBin
     *
     * @return $this
     */
    public function setMysqlBin($mysqlBin)
    {
        $this->mysqlBin = $mysqlBin;

        return $this;
    }

    /**
     * @param string $mysqlDumpBin
     *
     * @return $this
     */
    public function setMysqlDumpBin($mysqlDumpBin)
    {
        $this->mysqlDumpBin = $mysqlDumpBin;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dump($id, DatabaseConfigurationInterface $databaseConfiguration)
    {
        if ($databaseConfiguration->getPassword()) {
            putenv(sprintf('MYSQL_PWD=%s', $databaseConfiguration->getPassword()));
        }

        $database = sprintf('%s_%s_%s', self::SALT, $databaseConfiguration->getDbName(), $id);

        $user = $this->resolveUser($databaseConfiguration);
        $host = $this->resolveHost($databaseConfiguration);
        $port = $this->resolvePort($databaseConfiguration);

        $this->processExecutor->execute(
            $this->getVerifyDatabaseCommand($user, $host, $port, $databaseConfiguration->getDbName())
        );
        $this->processExecutor->execute($this->getDropDatabaseCommand($user, $host, $port, $database));
        $this->processExecutor->execute($this->getCreateDatabaseCommand($user, $host, $port, $database));
        $this->processExecutor->execute(
            $this->getDumpCommand($user, $host, $port, $databaseConfiguration->getDbName(), $database)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function restore($id, DatabaseConfigurationInterface $databaseConfiguration)
    {
        if ($databaseConfiguration->getPassword()) {
            putenv(sprintf('MYSQL_PWD=%s', $databaseConfiguration->getPassword()));
        }

        $database = sprintf('%s_%s_%s', self::SALT, $databaseConfiguration->getDbName(), $id);

        $user = $this->resolveUser($databaseConfiguration);
        $host = $this->resolveHost($databaseConfiguration);
        $port = $this->resolvePort($databaseConfiguration);

        $this->processExecutor->execute($this->getVerifyDatabaseCommand($user, $host, $port, $database));
        $this->processExecutor->execute(
            $this->getDropDatabaseCommand($user, $host, $port, $databaseConfiguration->getDbName())
        );
        $this->processExecutor->execute(
            $this->getCreateDatabaseCommand($user, $host, $port, $databaseConfiguration->getDbName())
        );
        $this->processExecutor->execute(
            $this->getDumpCommand($user, $host, $port, $database, $databaseConfiguration->getDbName())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedOs()
    {
        return [
            AbstractDatabaseIsolator::OS_WINDOWS,
            AbstractDatabaseIsolator::OS_LINUX,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            DatabaseConfigurationInterface::DRIVER_PDO_MYSQL,
        ];
    }

    /**
     * @param string $user
     * @param string $host
     * @param int $port
     * @param string $database
     *
     * @return string
     */
    protected function getVerifyDatabaseCommand($user, $host, $port, $database)
    {
        return sprintf(
            '%s -u %s -h %s --port %d -e "use "%s";"',
            $this->mysqlBin,
            $user,
            $host,
            $port,
            $database
        );
    }

    /**
     * @param string $user
     * @param string $host
     * @param int $port
     * @param string $database
     *
     * @return string
     */
    protected function getDropDatabaseCommand($user, $host, $port, $database)
    {
        return sprintf(
            '%s -u %s -h %s --port %d -e "DROP DATABASE IF EXISTS "%s";"',
            $this->mysqlBin,
            $user,
            $host,
            $port,
            $database
        );
    }

    /**
     * @param string $user
     * @param string $host
     * @param int $port
     * @param string $database
     *
     * @return string
     */
    protected function getCreateDatabaseCommand($user, $host, $port, $database)
    {
        return sprintf(
            '%s -u %s -h %s --port %d -e "CREATE DATABASE "%s";"',
            $this->mysqlBin,
            $user,
            $host,
            $port,
            $database
        );
    }

    /**
     * @param string $user
     * @param string $host
     * @param int $port
     * @param string $databaseFrom
     * @param string $databaseTo
     *
     * @return string
     */
    protected function getDumpCommand($user, $host, $port, $databaseFrom, $databaseTo)
    {
        return sprintf(
            '%s -u %s -h %s --port %d "%s" | %s -u %s -h %s --port %d "%s"',
            $this->mysqlDumpBin,
            $user,
            $host,
            $port,
            $databaseFrom,
            $this->mysqlBin,
            $user,
            $host,
            $port,
            $databaseTo
        );
    }

    /**
     * @param DatabaseConfigurationInterface $databaseConfiguration
     *
     * @return string
     */
    protected function resolveUser(DatabaseConfigurationInterface $databaseConfiguration)
    {
        return $databaseConfiguration->getUser() ?: 'root';
    }

    /**
     * @param DatabaseConfigurationInterface $databaseConfiguration
     *
     * @return string
     */
    protected function resolveHost(DatabaseConfigurationInterface $databaseConfiguration)
    {
        return $databaseConfiguration->getHost() ?: '127.0.0.1';
    }

    /**
     * @param DatabaseConfigurationInterface $databaseConfiguration
     *
     * @return int
     */
    protected function resolvePort(DatabaseConfigurationInterface $databaseConfiguration)
    {
        return $databaseConfiguration->getPort() ?: 3306;
    }
}
