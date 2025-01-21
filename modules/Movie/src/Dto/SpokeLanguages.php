<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\DataTransferObject;

class SpokeLanguages extends DataTransferObject
{
    public string $name;

    public string $iso_639_1;
}