<?php

namespace Siarko\ConfigFiles\Provider;

use Siarko\Api\State\AppState;
use Siarko\ConfigFiles\Api\Provider\LookupScopeComparatorInterface;
use Siarko\Files\Lookup\Result;

class LookupScopeComparator implements LookupScopeComparatorInterface
{

    public function compare(string $targetScope, Result $lookupResult): bool
    {
        $file = $lookupResult->getFile();
        $fileScope = basename($file->getPathInfo()->getDirname());
        if (ctype_upper($fileScope[0])) {
            $fileScope = AppState::SCOPE_DEFAULT;
        }
        return $fileScope === $targetScope;
    }
}