<?php

namespace Modules\Importer;

use Modules\DataTransferObject\DataTransferObject;
use Modules\Importer\Contracts\ParserInterface;

abstract class Parser implements ParserInterface
{
    public function __construct(
        protected DataTransferObject $dto,
        protected Rule $rules
    ) { }
}