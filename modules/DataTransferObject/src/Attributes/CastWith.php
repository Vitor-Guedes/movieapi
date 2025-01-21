<?php

namespace Modules\DataTransferObject\Attributes;

class CastWith
{
    public function __construct(
        protected $caster,
        protected $abstract
    ) {}
}