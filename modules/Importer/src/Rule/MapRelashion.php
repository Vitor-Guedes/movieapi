<?php

namespace Modules\Importer\Rule;

class MapRelashion
{
    public function __construct(
        protected string $main,
        protected string $with,
        protected string $alias = ''
    )
    {
        
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }
}