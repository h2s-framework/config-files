<?php

namespace Siarko\ConfigFiles\Provider;

use Siarko\ConfigFiles\Api\Provider\ConfigFileNameProviderInterface;

class ConfigFileNameProvider implements ConfigFileNameProviderInterface
{

    /**
     * @param array<string> $types
     */
    public function __construct(
        private readonly array $types = [],
    ){}

    /**
     * @param string $type
     * @return string
     */
    public function getFileName(string $type): string
    {
        if(array_key_exists($type, $this->types)){
            return $this->types[$type];
        }
        return $type;
    }
}