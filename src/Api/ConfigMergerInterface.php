<?php

namespace Siarko\ConfigFiles\Api;

interface ConfigMergerInterface
{

    /**
     * Merge two assoc arrays
     * @param array $base
     * @param array $override
     * @return array
     */
    public function merge(array $base, array $override): array;

}