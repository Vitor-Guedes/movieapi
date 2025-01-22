<?php

namespace Modules\DataTransferObject;

use Illuminate\Support\Arr;

class ArrayCaster
{
    public function cast($targetClass, $properties): array
    {
        if (is_string($properties)) {
            $decoded = json_decode($properties, true);

            if (json_last_error() == JSON_ERROR_SYNTAX) {
                $corrected = preg_replace('/""{2}/', '"', $properties);
                $decoded = json_decode(rtrim($corrected, '"'), true);
            }

            $properties = $decoded;
        }

        return Arr::map($properties, fn ($item) => new $targetClass($item));
    }
}