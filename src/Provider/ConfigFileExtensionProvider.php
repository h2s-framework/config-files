<?php

namespace Siarko\ConfigFiles\Provider;

class ConfigFileExtensionProvider
{

    /**
     * @param array[] $types
     */
    public function __construct(
        protected readonly array $extensions = ['yml','yaml'],
        protected readonly array $types = []
    )
    {
    }

    /**
     * @param string $type
     * @return string[]
     */
    public function getExtensions(string $type): array
    {
        if(!array_key_exists($type, $this->types)){
            return $this->extensions;
        }
        return array_unique(array_merge($this->types[$type], $this->extensions));
    }

    /**
     * @param string $type
     * @return string
     */
    public function getAsRegex(string $type): string
    {
        $extensions = $this->getExtensions($type);
        return '(' . implode('|', $extensions) . ')';
    }

}