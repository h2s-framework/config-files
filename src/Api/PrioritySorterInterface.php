<?php

namespace Siarko\ConfigFiles\Api;

interface PrioritySorterInterface
{

    /**
     * Sort items by priority
     * @param array $configs
     * @return array
     */
    public function sort(array $configs): array;
}