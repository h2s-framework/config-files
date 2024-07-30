<?php

namespace Siarko\ConfigFiles\Merger;

use Siarko\ConfigFiles\Api\ConfigMergerInterface;

class ConfigMerger implements ConfigMergerInterface
{

    /**
     * Almost the same as array_replace_recursive, but numeric keys are not replaced
     * @param array $base
     * @param array $override
     * @return array
     */
    public function merge(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (!array_key_exists($key, $base)) {
                $base[$key] = $value;
                continue;
            }
            if (is_array($value)) {
                if (is_array($base[$key])) {
                    $base[$key] = $this->merge($base[$key], $value);
                } else {
                    $base[$key] = $this->merge([$base[$key]], $value);
                }
            } else {
                if (is_array($base[$key])) {
                    $base[$key] = $this->merge($base[$key], [$value]);
                } else {
                    if (is_numeric($key)) {
                        $base[] = $value;
                    } else {
                        $base[$key] = $value;
                    }
                }
            }
        }
        return $base;
    }
}