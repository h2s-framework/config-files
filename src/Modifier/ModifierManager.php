<?php

namespace Siarko\ConfigFiles\Modifier;

use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\FixedArraySort;
use Siarko\ConfigFiles\Api\Modifier\ModifierInterface;
use Siarko\ConfigFiles\Api\Modifier\ModifierManagerInterface;
use Siarko\Files\Api\FileInterface;

class ModifierManager implements ModifierManagerInterface
{

    /**
     * @param ModifierInterface[] $modifiers
     * @throws \Exception
     */
    public function __construct(
        protected array $modifiers = []
    )
    {
        $this->modifiers = $this->sortModifiers($modifiers);
    }

    /**
     * @param FileInterface $file
     * @param array $config
     * @return array
     */
    public function applyModifications(FileInterface $file, array $config): array
    {
        foreach ($this->modifiers as $modifier) {
            $config = $modifier->apply($this, $file, $config);
        }
        return $config;
    }

    /**
     * @param array $modifiers
     * @return array
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    private function sortModifiers(array $modifiers = []): array
    {
        $sorter = new FixedArraySort();
        foreach ($this->getDependencyTree($modifiers) as $id => $deps) {
            $sorter->add($id, $deps);
        }
        $result = [];
        foreach ($sorter->sort() as $className) {
            $modifier = $this->getModifierByClass($className);
            if($modifier){
                $result[] = $modifier;
            }
        }
        return $result;
    }

    /**
     * Read all dependencies of off modifiers and construct tree
     * @param array $modifiers
     * @return array
     */
    private function getDependencyTree(array $modifiers = []): array
    {
        $dependencyOrder = [];
        foreach ($modifiers as $modifier) {
            $dependencyOrder[get_class($modifier)] = $modifier->getDependencyOrder();
        }
        $dependencyTree = [];
        foreach ($dependencyOrder as $className => $dependencies) {
            if(array_key_exists(ModifierInterface::DEPENDENCY_AFTER, $dependencies)){
                foreach ($dependencies[ModifierInterface::DEPENDENCY_AFTER] as $dependency) {
                    $dependencyTree[$dependency][] = $className;
                }
            }
            if(array_key_exists(ModifierInterface::DEPENDENCY_BEFORE, $dependencies)){
                foreach ($dependencies[ModifierInterface::DEPENDENCY_BEFORE] as $dependency) {
                    $dependencyTree[$className][] = $dependency;
                }
            }else{
                $dependencyTree[$className] = [];
            }
        }
        return $dependencyTree;
    }

    /**
     * @param string $className
     * @return ModifierInterface|null
     */
    private function getModifierByClass(string $className): ?ModifierInterface
    {
        $modifier = array_filter($this->modifiers, fn($modifier) => get_class($modifier) == $className);
        if(count($modifier) == 1){
            return current($modifier);
        }
        return null;
    }
}