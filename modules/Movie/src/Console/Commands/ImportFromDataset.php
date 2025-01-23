<?php

namespace Modules\Movie\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Importer\Import\ImportEloquent;
use Modules\Importer\Parser\ParserEloquent;
use Modules\Importer\Rule;
use Modules\Movie\Dto\GenresDto;
use Modules\Movie\Dto\KeywordsDto;
use Modules\Movie\Dto\MovieDto;
use Modules\Movie\Dto\ProductionCompanyDto;
use Modules\Movie\Dto\ProductionCountriesDto;
use Modules\Movie\Dto\SpokeLanguagesDto;
use Modules\Movie\Models\Genres;
use Modules\Movie\Models\Keyword;
use Modules\Movie\Models\Movie;
use Modules\Movie\Models\ProductionCompany;
use Modules\Movie\Models\ProductionCountry;
use Modules\Movie\Models\SpokenLanguage;
use Modules\Movie\Services\ImportService;

class ImportFromDataset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movieapp:import-dataset {--migrate : Executar migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa o filmes a partir de um datase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = __DIR__ . '/../../../dataset-tmdb/tmdb_5000_movies.csv';

        if (! file_exists($file)) {
            $this->error("Arquivo não encontrado: $file");
            return ;
        }

        if ($this->option('migrate')) {
            $this->databaseSetup();
        }

        $file = fopen($file, 'r');
        $movies = $columns = $ignered = [];

        while ($row = fgetcsv($file, null, ',')) {
            if (count($columns) == 0) {
                $columns = $row;
                $ignered[] = $row;
                continue ;
            }

            if (count($columns) != count($row)) {
                $this->warn("Ignored in line: ", count($movies));
                $ignered[] = $row;

                continue ;
            }

            $movies[] = new MovieDto(
                array_combine($columns, $row)
            );
        }

        fclose($file);

        $this->newLine();

        $this->withProgressBar($movies, function ($movie) {
            $this->import($movie);
        });
    }

    protected function databaseSetup()
    {
        $migrationsDir = __DIR__ . '/../../../database/migrations/*.php';

        $files = glob($migrationsDir);
        $files = Arr::map($files, fn ($file) => str_replace(".php", "", Arr::last(explode('/', $file))));

        $migrations = DB::table('migrations')->whereIn('migration', $files)->count();

        $this->info("Executando as migrations com o comando artisan migration:refresh");
        if ($migrations !== count($files)) {
            $this->call('migrate');
        } else {
            $this->call('migrate:refresh');
        }
    }

    protected function import($movie)
    {
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
        (new ImportEloquent(ParserEloquent::class, [$movie], $rules))->run();
    }
}
