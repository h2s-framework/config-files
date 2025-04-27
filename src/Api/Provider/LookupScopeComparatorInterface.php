<?php

namespace Siarko\ConfigFiles\Api\Provider;

use Siarko\Files\Lookup\Result;

interface LookupScopeComparatorInterface
{

    public function compare(string $targetScope, Result $lookupResult): bool;
}