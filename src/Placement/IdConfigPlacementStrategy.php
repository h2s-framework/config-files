<?php

namespace Siarko\ConfigFiles\Placement;

class IdConfigPlacementStrategy implements \Siarko\ConfigFiles\Api\ConfigPlacementStrategyInterface
{

    public const KEY_ID = 'id';

    private int $index = 0;

    /**
     * @param string $configType
     * @param array $configSet
     * @param array $config
     * @return array
     */
    public function addConfig(string $configType, array $configSet, array $config): array
    {
        if(array_key_exists(self::KEY_ID, $config)){
            $configSet[$config[self::KEY_ID]] = $config;
        }else{
            $configSet[$this->generateId($configType)] = $config;
        }
        return $configSet;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function generateId(string $type): string
    {
        return $type.'_' . $this->index++;
    }
}