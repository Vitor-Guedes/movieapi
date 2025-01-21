<?php

namespace Modules\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;
use ReflectionAttribute;
use Illuminate\Support\Arr;
use Modules\DataTransferObject\Attributes\CastWith;

abstract class DataTransferObject
{
    public function __construct($properties)
    {
        $this->assing($properties);
    }

    /**
     * @param array $properties
     * 
     * @return void
     */
    protected function assing(array $properties)
    {
        $class = new ReflectionClass($this);
        $publicProperties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($publicProperties as $property) {
            if ($castWith = $property->getAttributes(CastWith::class)) {
                $this->{$property->name} = $this->resolveCast($castWith[0], Arr::get($properties, $property->name, $property->getDefaultValue()));
                continue ;
            }
            $this->{$property->name} = Arr::get($properties, $property->name, $property->getDefaultValue());
        }
    }

    /**
     * @param ReflectionAttribute $castWith
     * @param array $properties
     * @return mixed
     */
    protected function resolveCast($castWith, $properties)
    {
        [$caster, $targetClass] = $castWith->getArguments();
        return app($caster)->cast(...compact('targetClass', 'properties'));
    }

    public function toArray(): array
    {
        return Arr::mapWithKeys(get_object_vars($this), fn ($value, $key) => 
            [$key => is_array($value) ? Arr::map($value, fn ($item) => $item->toArray()) : $value]
        );
    }
}