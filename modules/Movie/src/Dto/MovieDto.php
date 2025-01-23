<?php

namespace Modules\Movie\Dto;

use Modules\DataTransferObject\ArrayCaster;
use Modules\DataTransferObject\DataTransferObject;
use Modules\DataTransferObject\Attributes\CastWith;

class MovieDto extends DataTransferObject
{
    public string $budget = '';
    
    #[CastWith(ArrayCaster::class, GenresDto::class)]
    public array $genres;

    public string $homepage = '';

    public string|int $id = '';

    #[CastWith(ArrayCaster::class, KeywordsDto::class)]
    public array $keywords;

    public string $original_language = '';

    public string $original_title = '';

    public string $overview = '';

    public string $popularity = '';

    #[CastWith(ArrayCaster::class, ProductionCompanyDto::class)]
    public array $production_companies;

    #[CastWith(ArrayCaster::class, ProductionCountriesDto::class)]
    public array $production_countries;

    public string $release_date = '';

    public string|int $revenue = '';

    public string|int $runtime = '';

    #[CastWith(ArrayCaster::class, SpokeLanguagesDto::class)]
    public array $spoken_languages;

    public string $status = '';

    public string $tagline = '';

    public string $title = '';

    public string|float $vote_average = '';

    public string|int $vote_count = '';
}