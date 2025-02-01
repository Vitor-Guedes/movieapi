<?php

namespace Modules\Movie;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GoogleSearchClient
{
    public function __construct(
        protected string $google_cse_url,
        protected string $google_cse_id,
        protected string $google_cse_key,
        protected int $quantity = 10,
        protected int $maxRequests = 90,
        protected string $fileRequests = 'google_search/requests.json'
    )
    {
        $this->checkCredentials();

        $this->checkRequestFiles();
    }

    /**
     * @return void
     */
    protected function checkCredentials(): void
    {
        $invalidCredentials = empty($this->google_cse_id)
            || empty($this->google_cse_key)
                || empty($this->google_cse_url);

        if ($invalidCredentials) {
            throw new Exception("Credenciais para requisição inválidas");
        }
    }

    protected function checkRequestFiles()
    {
        $exists = Storage::disk('local')->exists($this->fileRequests);

        if (! $exists) {
            Storage::disk('local')->put($this->fileRequests, json_encode([]));
        }
    }


    /**
     * @param string $term
     * 
     * @return array
     */
    public function get(string $term): array
    {
        if (! $this->canMakeRequest()) {
            throw new Exception('quantidade de Requisições diarias excedidas');
        }

        $response = Http::get($this->buildUrl($term));

        $this->registerRequest();

        return $response->successful() ?
            $response->json()
                : [];
    }

    /**
     * @param bool
     */
    protected function canMakeRequest(): bool
    {
        $json = Storage::disk('local')->get($this->fileRequests);
        $json = json_decode($json, true);

        $now = Carbon::now();
        $today = $now->format('d-m-y');
        $count = $json[$today] ?? 0;

        return $count < $this->maxRequests;
    }

    /**
     * @param string $term
     * 
     * @return string
     */
    public function buildUrl(string $term): string
    {
        return "$this->google_cse_url?" . http_build_query([
            'q' => $term,
            'cx' => $this->google_cse_id,
            'key' => $this->google_cse_key,
            'searchType' => 'image',
            'num' => $this->quantity,
            'imgSize' => 'medium'
        ]);
    }

    /**
     * @return void
     */
    protected function registerRequest(): void
    {
        $json = Storage::disk('local')->get($this->fileRequests);
        $json = json_decode($json, true);

        $now = Carbon::now();
        $today = $now->format('d-m-y');

        $count = $json[$today] ?? 0;
        $json[$today] = $count + 1;
        Storage::disk('local')->put($this->fileRequests, json_encode($json));
    }
}