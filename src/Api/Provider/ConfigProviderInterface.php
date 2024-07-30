<?php

namespace Siarko\ConfigFiles\Api\Provider;

interface ConfigProviderInterface
{

    /**
     * Get config by type
     * @param string $type
     * @return array
     */
    public function fetch(string $type): array;

}