<?php

namespace Siarko\ConfigFiles\Api\Modifier;

use Siarko\Files\Api\FileInterface;

interface ModifierInterface
{

    public const DEPENDENCY_AFTER = 'after'; //Marks modifiers that should be applied after this one
    public const DEPENDENCY_BEFORE = 'before'; //Marks modifiers that should be applied before this one

    /**
     * @return string[][] - list of modifiers that should be applied before/after
     */
    public function getDependencyOrder(): array;

    /**
     * If this method returns empty array - config is skipped
     * @param ModifierManagerInterface $manager
     * @param FileInterface $file
     * @param array $config
     * @return array
     */
    public function apply(ModifierManagerInterface $manager, FileInterface $file, array $config): array;

}