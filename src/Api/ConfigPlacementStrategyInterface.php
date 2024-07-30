<?php

namespace Siarko\ConfigFiles\Api;

interface ConfigPlacementStrategyInterface
{

    /**
     * Adds config to the set
     * @param string $configType
     * @param array $configSet
     * @param array $config
     * @return array
     */
    public function addConfig(string $configType, array $configSet, array $config): array;
}