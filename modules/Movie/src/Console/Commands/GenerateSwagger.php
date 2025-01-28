<?php

namespace Modules\Movie\Console\Commands;

use OpenApi\Attributes as OA;
use Illuminate\Console\Command;

#[OA\OpenApi(
    info: new OA\Info(
        version: '1.0.0',
        title: 'MovieApp',
        description: 'Api para vizualização de informações de filmes migrados a partir de uma dataset',
        contact: new OA\Contact(
            name: 'Vitor Guedes',
            email: 'vtrf2.0@gmail.com'
        )
    ),
    servers: [
        new OA\Server(
            url: '{protocol}://dev.backend.com',
            description: 'Ambiente local',
            variables: [
                'protocol' => new OA\ServerVariable(
                    serverVariable: 'protocol',
                    default: 'http',
                    enum: ['http', 'https'],
                    description: 'Protocolo usado'
                )
            ]
        )
    ],
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                type: 'http',
                in: 'header',
                scheme: 'bearer',
                bearerFormat: 'JWT',
                name: 'Authorization',
                securityScheme: 'bearer_auth',
                description: 'Token de acesso',
            )
        ]
    ),
    tags: [
        new OA\Tag(
            name: "Users",
            description: "Gerenciamento de usuários e token de acesso"
        ),
        new OA\Tag(
            name: "Movies",
            description: "Endpoints de busca dos filmes"
        ),
        new OA\Tag(
            name: "Review",
            description: "Gerenciamento de reviews dos usuários"
        ),
    ]
)]
class GenerateSwagger extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movieapp:swagger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera a documentação da api';

   
    public function handle()
    {
        $openapi = \OpenApi\Generator::scan(config('movie.swagger.files'));
        /** @todo: Replace Variaveis de ambiente */
        $json = $openapi->toJson();
        file_put_contents(config('movie.swagger.dist'), $json);
    }
}