<?php

namespace Akuma\DatabaseIsolation\Isolator;

use Akuma\DatabaseIsolation\Model\DatabaseConfigurationInterface;

abstract class AbstractDatabaseIsolator implements DatabaseIsolatorInterface
{
    const OS_WINDOWS = 'WINDOWS';
    const OS_LINUX = 'LINUX';
    const OS_MAC = 'MAC';
    const OS_UNKNOWN = 'UNKNOWN';

    /**
     * @return string
     */
    protected function getCurrentOs()
    {
        switch (PHP_OS) {
            case 'WINNT':
                return self::OS_WINDOWS;
                break;
            case 'Linux':
                return self::OS_LINUX;
                break;
            default:
                return self::OS_UNKNOWN;
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigurationSupported(DatabaseConfigurationInterface $databaseConfiguration)
    {
        return $databaseConfiguration->isValid() &&
        in_array($databaseConfiguration->getDriver(), $this->getSupportedDrivers(), true) &&
        in_array($this->getCurrentOs(), $this->getSupportedOs(), true);
    }

    /**
     * @return array
     */
    abstract public function getSupportedOs();

    /**
     * @return array
     */
    abstract public function getSupportedDrivers();
}
