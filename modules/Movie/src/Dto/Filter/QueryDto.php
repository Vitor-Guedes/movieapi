<?php

namespace Modules\Movie\Dto\Filter;

use Modules\DataTransferObject\DataTransferObject;
use Modules\DataTransferObject\Attributes\CastWith;
use Modules\DataTransferObject\ArrayCaster;

class QueryDto extends DataTransferObject
{
    public string $field = '';

    public string $operator = '';

    public mixed $value = '';

    #[CastWith(ArrayCaster::class, QueryDto::class)]
    public array $and = [];

    #[CastWith(ArrayCaster::class, QueryDto::class)]
    public array $or = [];

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->field != ''
            && $this->operator != ''
            && $this->value != '';
    }

    /**
     * @param string $type ['and', 'or']
     * 
     * @return bool
     */
    public function has($type): bool
    {
        return isset($this->{$type}) && count($this->{$type}) > 0;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        if ($this->operator == 'lk') {
            return strpos($this->value, '%') === false 
                ? "%$this->value%"
                    : $this->value;
        }
        return $this->value;
    }

    public function getOperator()
    {
        return match ($this->operator) {
            default => '=',
            'lk' => 'like',
            'eq' => '=',
            'gt' => '>',
            'lt' => '<'
        };
    }

    /**
     * @return array
     */
    public function toWhereArray(): array
    {
        return [
            $this->getField(),
            $this->getOperator(),
            $this->getValue()
        ];
    }
}