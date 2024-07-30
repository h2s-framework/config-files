<?php

namespace Siarko\ConfigFiles\Sorter;

use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\FixedArraySort;
use Siarko\ConfigFiles\Api\PrioritySorterInterface;

class TopologicalSort implements PrioritySorterInterface
{

    /**
     * @param string $dependencyKey
     */
    public function __construct(
        private readonly string $dependencyKey = 'dependencies'
    )
    {
    }

    /**
     * Assumes that each config is located under its own key and keys are referenced in dependencies
     * @param array $configs
     * @return array
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    public function sort(array $configs): array
    {
        $sorter = new FixedArraySort();
        foreach ($configs as $id => $data) {
            $sorter->add($id, $data[$this->dependencyKey] ?? []);
        }
        return $sorter->sort();
    }


}