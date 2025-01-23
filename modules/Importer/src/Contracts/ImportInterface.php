<?php

namespace Modules\Importer\Contracts;

interface ImportInterface
{
    public function register($subject): mixed;
}