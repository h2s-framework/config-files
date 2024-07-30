<?php

namespace Siarko\ConfigFiles\Api\Modifier;

use Exception;
use Siarko\Files\Api\FileInterface;

interface ModifierManagerInterface
{

    /**
     * Apply all registered modifiers to config
     * @param FileInterface $file
     * @param array $config
     * @return array
     * @throws Exception
     */
    public function applyModifications(FileInterface $file, array $config): array;
}