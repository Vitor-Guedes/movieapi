<?php

namespace Modules\Movie\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Movie\Dto\MovieDto;

class ImportFromDataset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movieapp:import-dataset';

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

        $file = fopen($file, 'r');
        $movies = $columns = [];
        while(count($movies) <= 10) {
            $row = fgetcsv($file, null, ',');
            if (count($columns) == 0) {
                $columns = $row;
                continue ;
            }

            $movieDto = new MovieDto(
                array_combine($columns, $row)
            );
            $movies[] = $movieDto->toArray();

            // app(Import::class)->run($movieDto); 
        }

        fclose($file);
    }
}
