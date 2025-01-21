<?php

namespace Modules\DataTransferObject;

use Illuminate\Support\Arr;

class ArrayCaster
{
    public function cast($targetClass, $properties): array
    {
        if (is_string($properties)) {
            $properties = json_decode($properties, true);
        }
        return Arr::map($properties, fn ($item) => new $targetClass($item));
    }
}