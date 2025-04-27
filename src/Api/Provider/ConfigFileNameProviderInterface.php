<?php

namespace Siarko\ConfigFiles\Api\Provider;

interface ConfigFileNameProviderInterface
{

    public function getFileName(string $type): string;
}