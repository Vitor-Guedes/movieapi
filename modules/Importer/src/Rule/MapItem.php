<?php

namespace Modules\Importer\Rule;

class MapItem
{
    public function __construct(
        protected string $from,
        protected string $to,
        protected string $alias = ''
    )
    { }

     /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return object
     */
    public function createNewTo(): object
    {
        return app($this->to);
    }
}