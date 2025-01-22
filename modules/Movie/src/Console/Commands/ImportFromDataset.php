<?php

namespace Modules\Movie\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Movie\Dto\MovieDto;
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

        $importService = app(ImportService::class);
        $this->withProgressBar($movies, function ($movie) use ($importService) {
            $importService->run($movie);
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
}
