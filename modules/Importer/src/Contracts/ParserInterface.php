<?php

namespace Modules\Importer\Contracts;

interface ParserInterface
{
    public function parse(): mixed;
}