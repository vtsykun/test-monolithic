<?php

namespace Akuma\DatabaseIsolation\Isolator;

use Akuma\DatabaseIsolation\Model\DatabaseConfigurationInterface;

interface DatabaseIsolatorInterface
{
    /**
     * @param DatabaseConfigurationInterface $databaseConfiguration
     *
     * @return bool
     */
    public function isConfigurationSupported(DatabaseConfigurationInterface $databaseConfiguration);

    /**
     * @param mixed $id
     * @param DatabaseConfigurationInterface $databaseConfiguration
     *
     * @return string
     */
    public function dump($id, DatabaseConfigurationInterface $databaseConfiguration);

    /**
     * @param mixed $id
     * @param DatabaseConfigurationInterface $databaseConfiguration
     *
     * @return mixed
     */
    public function restore($id, DatabaseConfigurationInterface $databaseConfiguration);
}
