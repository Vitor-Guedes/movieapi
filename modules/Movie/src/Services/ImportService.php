<?php

namespace Modules\Movie\Services;

use Illuminate\Support\Facades\DB;
use Modules\DataTransferObject\DataTransferObject;
use Modules\Movie\Import\Parser;

class ImportService
{
    /**
     * @todo: Depois Cria um modulo só para import e export de dados,
     * Refatorar classes, distribuir melhor responsabilidades e realizar testes
     */
    public function run(DataTransferObject $dto)
    {
        DB::transaction(function () use ($dto) {
            return $this->parser($dto, [
                'map' => [
                    \Modules\Movie\Dto\MovieDto::class => [
                        'movie' => \Modules\Movie\Models\Movie::class,
                    ],
                    \Modules\Movie\Dto\GenresDto::class => [
                        'genres' => \Modules\Movie\Models\Genres::class
                    ],
                    \Modules\Movie\Dto\KeywordsDto::class => [
                        'keywords' => \Modules\Movie\Models\Keyword::class
                    ],
                    \Modules\Movie\Dto\ProductionCompanyDto::class => [
                        'production_companies' => \Modules\Movie\Models\ProductionCompany::class
                    ],
                    \Modules\Movie\Dto\ProductionCountriesDto::class => [
                        'production_countries' => \Modules\Movie\Models\ProductionCountry::class
                    ],
                    \Modules\Movie\Dto\SpokeLanguagesDto::class => [
                        'spoke_languages' => \Modules\Movie\Models\SpokeLanguage::class
                    ],
                ],
    
                'relashions' => [
                    'movie:genres',
                    'movie:keywords',
                    'movie:production_companies',
                    'movie:production_countries',
                    'movie:spoke_languages',
                ]
            ])->parse();
        });
    }

    protected function parser($dto, array $rules)
    {
        return new Parser($dto, $rules);
    }
}