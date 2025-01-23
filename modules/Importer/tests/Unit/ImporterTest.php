<?php

namespace Modules\Importer\Tests\Unit;

use Tests\TestCase;
use Modules\Importer\Rule;
use Modules\Movie\Dto\MovieDto;
use Modules\Movie\Models\Movie;
use Modules\Movie\Models\Genres;
use Modules\Movie\Dto\GenresDto;
use Modules\Movie\Models\Keyword;
use Modules\Importer\Rule\MapItem;
use Modules\Movie\Dto\KeywordsDto;
use Modules\Importer\Rule\MapRelashion;
use Modules\Movie\Dto\SpokeLanguagesDto;
use Modules\Movie\Models\SpokenLanguage;
use Modules\Importer\Import\ImportEloquent;
use Modules\Importer\Parser\ParserEloquent;
use Modules\Movie\Dto\ProductionCompanyDto;
use Modules\Movie\Dto\ProductionCountriesDto;
use Modules\Movie\Models\ProductionCompany;
use Modules\Movie\Models\ProductionCountry;
use Modules\Importer\Contracts\ImportInterface;
use Modules\Importer\Contracts\ParserInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_be_able_to_create_an_instance_of_the_rule_class()
    {
        $rules = new Rule(map: [
            [
                'from' => GenresDto::class,
                'to' => Genres::class,
                'alias' => 'genres'
            ]
        ], relashions: [
            [
                'main' => Movie::class,
                'with' => Genres::class,
                'alias' => 'genres'
            ]
        ]);

        $this->assertInstanceOf(Rule::class, $rules);
        $this->assertInstanceOf(MapItem::class, $rules->getMapItem('genres'));
        $this->assertInstanceOf(MapRelashion::class, $rules->getMapRelashion('genres'));
        $this->assertInstanceOf(Genres::class, $rules->getMapItem('genres')->createNewTo());
        $this->assertCount(1, $rules->getMapItems());
        $this->assertCount(1, $rules->getMapRelashions());
    }

    public function test_must_be_able_to_create_a_parser_class_that_transforms_dto_into_models()
    {
        [$dto, $rules] = $this->createDtoAndRules();
        $parser = new ParserEloquent($dto, $rules);
        
        $parsed = $parser->parse();

        $this->assertInstanceOf(ParserInterface::class, $parser);
        $this->assertInstanceOf(Movie::class, $parsed);
        $this->assertCount(4, $parsed->getRelation('genres'));
        $this->assertEquals($dto->genres[0]->toArray(), $parsed->getRelation('genres')[0]->toArray());
        $this->assertCount(2, $parsed->getRelation('spoken_languages'));
        $this->assertEquals($dto->spoken_languages[1]->toArray(), $parsed->getRelation('spoken_languages')[1]->toArray());
    }

    public function test_must_be_able_to_create_records_with_the_relationships()
    {
        [$dto, $rules] = $this->createDtoAndRules();
        $collection = [$dto];

        $import = new ImportEloquent(ParserEloquent::class, $collection, $rules);
        $import->run();

        $this->assertInstanceOf(ImportInterface::class, $import);
        $this->assertInstanceOf(ParserInterface::class, $import->getParser($dto));
        $this->assertCount(1, $import->getCollection());
        $this->assertDatabaseCount('movies', 1);
        $this->assertDatabaseCount('genres', 4);
        $this->assertDatabaseCount('spoken_languages', 2);
    }

    public function test_must_be_able_to_import_n_records_and_their_relationships()
    {
        $csvRows = [
            '237000000,"[{""id"": 28, ""name"": ""Action""}, {""id"": 12, ""name"": ""Adventure""}, {""id"": 14, ""name"": ""Fantasy""}, {""id"": 878, ""name"": ""Science Fiction""}]",http://www.avatarmovie.com/,19995,"[{""id"": 1463, ""name"": ""culture clash""}, {""id"": 2964, ""name"": ""future""}, {""id"": 3386, ""name"": ""space war""}, {""id"": 3388, ""name"": ""space colony""}, {""id"": 3679, ""name"": ""society""}, {""id"": 3801, ""name"": ""space travel""}, {""id"": 9685, ""name"": ""futuristic""}, {""id"": 9840, ""name"": ""romance""}, {""id"": 9882, ""name"": ""space""}, {""id"": 9951, ""name"": ""alien""}, {""id"": 10148, ""name"": ""tribe""}, {""id"": 10158, ""name"": ""alien planet""}, {""id"": 10987, ""name"": ""cgi""}, {""id"": 11399, ""name"": ""marine""}, {""id"": 13065, ""name"": ""soldier""}, {""id"": 14643, ""name"": ""battle""}, {""id"": 14720, ""name"": ""love affair""}, {""id"": 165431, ""name"": ""anti war""}, {""id"": 193554, ""name"": ""power relations""}, {""id"": 206690, ""name"": ""mind and soul""}, {""id"": 209714, ""name"": ""3d""}]",en,Avatar,"In the 22nd century, a paraplegic Marine is dispatched to the moon Pandora on a unique mission, but becomes torn between following orders and protecting an alien civilization.",150.437577,"[{""name"": ""Ingenious Film Partners"", ""id"": 289}, {""name"": ""Twentieth Century Fox Film Corporation"", ""id"": 306}, {""name"": ""Dune Entertainment"", ""id"": 444}, {""name"": ""Lightstorm Entertainment"", ""id"": 574}]","[{""iso_3166_1"": ""US"", ""name"": ""United States of America""}, {""iso_3166_1"": ""GB"", ""name"": ""United Kingdom""}]",2009-12-10,2787965087,162,"[{""iso_639_1"": ""en"", ""name"": ""English""}, {""iso_639_1"": ""es"", ""name"": ""Espa\u00f1ol""}]",Released,Enter the World of Pandora.,Avatar,7.2,11800',
            '245000000,"[{""id"": 28, ""name"": ""Action""}, {""id"": 12, ""name"": ""Adventure""}, {""id"": 80, ""name"": ""Crime""}]",http://www.sonypictures.com/movies/spectre/,206647,"[{""id"": 470, ""name"": ""spy""}, {""id"": 818, ""name"": ""based on novel""}, {""id"": 4289, ""name"": ""secret agent""}, {""id"": 9663, ""name"": ""sequel""}, {""id"": 14555, ""name"": ""mi6""}, {""id"": 156095, ""name"": ""british secret service""}, {""id"": 158431, ""name"": ""united kingdom""}]",en,Spectre,"A cryptic message from Bond’s past sends him on a trail to uncover a sinister organization. While M battles political forces to keep the secret service alive, Bond peels back the layers of deceit to reveal the terrible truth behind SPECTRE.",107.376788,"[{""name"": ""Columbia Pictures"", ""id"": 5}, {""name"": ""Danjaq"", ""id"": 10761}, {""name"": ""B24"", ""id"": 69434}]","[{""iso_3166_1"": ""GB"", ""name"": ""United Kingdom""}, {""iso_3166_1"": ""US"", ""name"": ""United States of America""}]",2015-10-26,880674609,148,"[{""iso_639_1"": ""fr"", ""name"": ""Fran\u00e7ais""}, {""iso_639_1"": ""en"", ""name"": ""English""}, {""iso_639_1"": ""es"", ""name"": ""Espa\u00f1ol""}, {""iso_639_1"": ""it"", ""name"": ""Italiano""}, {""iso_639_1"": ""de"", ""name"": ""Deutsch""}]",Released,A Plan No One Escapes,Spectre,6.3,4466',
            '250000000,"[{""id"": 28, ""name"": ""Action""}, {""id"": 12, ""name"": ""Adventure""}, {""id"": 14, ""name"": ""Fantasy""}, {""id"": 878, ""name"": ""Science Fiction""}]",http://www.x-menmovies.com/,127585,"[{""id"": 1228, ""name"": ""1970s""}, {""id"": 1852, ""name"": ""mutant""}, {""id"": 4379, ""name"": ""time travel""}, {""id"": 8828, ""name"": ""marvel comic""}, {""id"": 9717, ""name"": ""based on comic book""}, {""id"": 10761, ""name"": ""superhuman""}, {""id"": 14527, ""name"": ""storm""}, {""id"": 161271, ""name"": ""beast""}, {""id"": 179430, ""name"": ""aftercreditsstinger""}, {""id"": 206736, ""name"": ""changing the past or future""}]",en,X-Men: Days of Future Past,The ultimate X-Men ensemble fights a war for the survival of the species across two time periods as they join forces with their younger selves in an epic battle that must change the past – to save our future.,118.078691,"[{""name"": ""Twentieth Century Fox Film Corporation"", ""id"": 306}, {""name"": ""Donners\' Company"", ""id"": 431}, {""name"": ""Marvel Entertainment"", ""id"": 7505}, {""name"": ""Bad Hat Harry Productions"", ""id"": 9168}, {""name"": ""TSG Entertainment"", ""id"": 22213}, {""name"": ""Down Productions"", ""id"": 37336}, {""name"": ""Revolution Sun Studios"", ""id"": 76043}]","[{""iso_3166_1"": ""GB"", ""name"": ""United Kingdom""}, {""iso_3166_1"": ""US"", ""name"": ""United States of America""}]",2014-05-15,747862775,131,"[{""iso_639_1"": ""en"", ""name"": ""English""}]",Released,"To save the future, they must alter the past",X-Men: Days of Future Past,7.5,6032'
        ];
        $dtos = array_map(function ($row) use ($csvRows) {
            $_row = array_combine(
                ['budget','genres','homepage','id','keywords','original_language','original_title','overview','popularity','production_companies','production_countries','release_date','revenue','runtime','spoken_languages','status','tagline','title','vote_average','vote_count'],
                str_getcsv($row, ',')
            );
            return new MovieDto($_row);
        }, $csvRows);
        $rules = new Rule(map: [
            [
                'from' => MovieDto::class,
                'to' => Movie::class,
                'alias' => 'movie'
            ],
            [
                'from' => GenresDto::class,
                'to' => Genres::class,
                'alias' => 'genres'
            ],
            [
                'from' => KeywordsDto::class,
                'to' => Keyword::class,
                'alias' => 'keywords'
            ],
            [
                'from' => ProductionCompanyDto::class,
                'to' => ProductionCompany::class,
                'alias' => 'production_companies'
            ],
            [
                'from' => ProductionCountriesDto::class,
                'to' => ProductionCountry::class,
                'alias' => 'production_countries'
            ],
            [
                'from' => SpokeLanguagesDto::class,
                'to' => SpokenLanguage::class,
                'alias' => 'spoken_languages'
            ]
        ], relashions: [
            [
                'main' => Movie::class,
                'with' => Genres::class,
                'alias' => 'genres'
            ],
            [
                'main' => Movie::class,
                'with' => Keyword::class,
                'alias' => 'keywords'
            ],
            [
                'main' => Movie::class,
                'with' => ProductionCompany::class,
                'alias' => 'production_companies'
            ],
            [
                'main' => Movie::class,
                'with' => ProductionCountry::class,
                'alias' => 'production_countries'
            ],
            [
                'main' => Movie::class,
                'with' => SpokenLanguage::class,
                'alias' => 'spoken_languages'
            ]
        ]);

        $import = new ImportEloquent(ParserEloquent::class, $dtos, $rules);
        $import->run();

        $genresIds = array_map(function ($genre) {
            return $genre->id;
        }, array_merge($dtos[0]->genres, $dtos[1]->genres, $dtos[2]->genres));

        $keywordsIds = array_map(function ($keyword) {
            return $keyword->id;
        }, array_merge($dtos[0]->keywords, $dtos[1]->keywords, $dtos[2]->keywords));

        $productionCompaniesIds = array_map(function ($productionCompany) {
            return $productionCompany->id;
        }, array_merge($dtos[0]->production_companies, $dtos[1]->production_companies, $dtos[2]->production_companies));

        $productionCountriesIso = array_map(function ($productionCountry) {
            return $productionCountry->iso_3166_1;
        }, array_merge($dtos[0]->production_countries, $dtos[1]->production_countries, $dtos[2]->production_countries));

        $spokenLanguagesIso = array_map(function ($spokenLanguage) {
            return $spokenLanguage->iso_639_1;
        }, array_merge($dtos[0]->spoken_languages, $dtos[1]->spoken_languages, $dtos[2]->spoken_languages));

        $this->assertDatabaseCount('movies', 3);
        $this->assertDatabaseCount('genres', count(array_unique($genresIds)));
        $this->assertDatabaseCount('keywords', count(array_unique($keywordsIds)));
        $this->assertDatabaseCount('production_companies', count(array_unique($productionCompaniesIds)));
        $this->assertDatabaseCount('production_countries', count(array_unique($productionCountriesIso)));
        $this->assertDatabaseCount('spoken_languages', count(array_unique($spokenLanguagesIso)));
    }

    protected function createDtoAndRules()
    {
        $columns = ['budget','genres','homepage','id','keywords','original_language','original_title','overview','popularity','production_companies','production_countries','release_date','revenue','runtime','spoken_languages','status','tagline','title','vote_average','vote_count'];
        $csv = '237000000,"[{""id"": 28, ""name"": ""Action""}, {""id"": 12, ""name"": ""Adventure""}, {""id"": 14, ""name"": ""Fantasy""}, {""id"": 878, ""name"": ""Science Fiction""}]",http://www.avatarmovie.com/,19995,"[{""id"": 1463, ""name"": ""culture clash""}, {""id"": 2964, ""name"": ""future""}, {""id"": 3386, ""name"": ""space war""}, {""id"": 3388, ""name"": ""space colony""}, {""id"": 3679, ""name"": ""society""}, {""id"": 3801, ""name"": ""space travel""}, {""id"": 9685, ""name"": ""futuristic""}, {""id"": 9840, ""name"": ""romance""}, {""id"": 9882, ""name"": ""space""}, {""id"": 9951, ""name"": ""alien""}, {""id"": 10148, ""name"": ""tribe""}, {""id"": 10158, ""name"": ""alien planet""}, {""id"": 10987, ""name"": ""cgi""}, {""id"": 11399, ""name"": ""marine""}, {""id"": 13065, ""name"": ""soldier""}, {""id"": 14643, ""name"": ""battle""}, {""id"": 14720, ""name"": ""love affair""}, {""id"": 165431, ""name"": ""anti war""}, {""id"": 193554, ""name"": ""power relations""}, {""id"": 206690, ""name"": ""mind and soul""}, {""id"": 209714, ""name"": ""3d""}]",en,Avatar,"In the 22nd century, a paraplegic Marine is dispatched to the moon Pandora on a unique mission, but becomes torn between following orders and protecting an alien civilization.",150.437577,"[{""name"": ""Ingenious Film Partners"", ""id"": 289}, {""name"": ""Twentieth Century Fox Film Corporation"", ""id"": 306}, {""name"": ""Dune Entertainment"", ""id"": 444}, {""name"": ""Lightstorm Entertainment"", ""id"": 574}]","[{""iso_3166_1"": ""US"", ""name"": ""United States of America""}, {""iso_3166_1"": ""GB"", ""name"": ""United Kingdom""}]",2009-12-10,2787965087,162,"[{""iso_639_1"": ""en"", ""name"": ""English""}, {""iso_639_1"": ""es"", ""name"": ""Espa\u00f1ol""}]",Released,Enter the World of Pandora.,Avatar,7.2,11800';
        $array = array_combine($columns, str_getcsv($csv));
        $dto = new MovieDto($array);
        $rules = new Rule(map: [
            [
                'from' => MovieDto::class,
                'to' => Movie::class,
                'alias' => 'movie'
            ],
            [
                'from' => GenresDto::class,
                'to' => Genres::class,
                'alias' => 'genres'
            ],
            [
                'from' => SpokeLanguagesDto::class,
                'to' => SpokenLanguage::class,
                'alias' => 'spoken_languages'
            ]
        ], relashions: [
            [
                'main' => Movie::class,
                'with' => Genres::class,
                'alias' => 'genres'
            ],
            [
                'main' => Movie::class,
                'with' => SpokenLanguage::class,
                'alias' => 'spoken_languages'
            ]
        ]);
        return [$dto, $rules];
    }
}