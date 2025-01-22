# Movie Api

Projeto de aprendizado, objetivo é pegar uma dataset importar os dados para aplicação e consulmir os dados via api RESTFUL. Implmentar funcinoalidades para vizualização dos filmes e criação de reviews, que só devem ser criadas com autenticação.

# Instalação
Instalar as dempendencias e subir a base juntocom as migrations do projeto
```bash
# diretorio do projeto
composer install

php artisan movieapp:import-dataset --migrate
```

# Api

## Auth

<details>
<summary> <code>POST /api/user (Cria uma conta) </code> </summary>
Endpoint para criar um usuário no sistema

### Request 
```json
{
    "nome": "John Doe",
    "email": "johndoe@email.com",
    "password": "1a3b5c",
    "comfirmed_password": "1a3b5c"
}
```

### Response 201

### Response 422
```json
[
    {
        "email": [
            "The email field is required"
        ],
        "confirmed_password": [
            ...
        ]
    }
]
```
</details>

<details>
<summary> <code>POST /api/auth (Gera um acess_token) </code> </summary>
Endpoint para criar um token_access para consulmir a api.

### Request 
```json
{
    "email": "johndoe@email.com",
    "password": "1a3b5c"
}
```

### Response
```json
// HTTP CODE 200
{
    "access_token": "{$token}",
    "expire": 3600
}
```
</details>

## Movies

<details>
<summary> <code>GET /api/movies (Lista os filmes) </code> </summary>
Endpoint para listar os filmes. A lista retornar por padão 10 registros.


### Response 200
```json
[
    "data": [
        {
            "id": "",
            "budget": "",
            "genres": [
                {
                    "id": "",
                    "name": ""
                },
                ...
            ],
            "homepage": "",
            "keywords": [
                {
                    "id": "",
                    "name": ""
                },
                ...
            ],
            "original_language": "",
            "original_title": "",
            "overview": "",
            "popularity": "",
            "production_companies": [
                {
                    "id": "",
                    "name": ""
                },
                ...
            ],
            "production_countries": [
                {
                    "name": "",
                    "iso_3166_1": ""
                },
                ...
            ],
            "release_date": "",
            "revenue": "",
            "runtime": "",
            "spoken_languages": [
                {
                    "name": "",
                    "iso_639_1": ""
                },
                ...
            ],
            "status": "",
            "tagline": "",
            "title": "",
            "vote_average": "",
            "vote_count": "",
        },
        {
            ...
        }
    ],
    // Paginate Data
    "count": 10,
    "next_page": "",
    "curr_page": ""
]
```
</details>

## Movies Borders (genres, keywords, production_companies, production_countries, spoken_languages)

<details>
<summary> <code>GET /api/movies/{:movie_id}/{:border} (Lista os filmes) </code> </summary>
Endpoint para listar os generos de um filme. A lista retornar por padão 10 registros.


### Response 200
```json
[
    "movie_id": "",
    ":border": [
        {
            "id": "",
            "name": ""
        },
        ...
    ],
    // Paginate Data
    "count": 10,
    "next_page": "",
    "curr_page": ""
]
```
</details>


## Filters and Modifiers

<details>
<summary> <code>GET /api/movies/{:movie_id}/{:border} (Lista os filmes) </code> </summary>
Filtros e modificadores para ajustar o retorno da listagem de filems

> | filter/modify | accept parametes | description |
> | ------------- | ---------------- | ----------- |
> | limit | min:1 max:100 | Número de registros por busca |
> | sort | sort=id:desc | Ordenação dos registro |
> | fields | fields=id,title,overview | Filtra os campos da entidade principal (movie) |
> | sub fields | fields=id,title,keywords.name,spoken_languages.iso_639_1 | Filtra os campos da entidade principal (movie) junto com entidade de bordas |
> | query | query={:field}:{:op}:{:value} | Filtra os registro por campo, operador e valor. <a href="#fields-and-operators">Campos e Operadores</a> |

<h3 id="fields-and-operators">
Campos e operadores permitidos na Query
</h3>

Campos

> | fields | type data | description |
> | ------ | --------- | ----------- |
> | title | stirng |  |
> | original_title | stirng |  |
> | overview | string | |
> | status | string |  |
> | budget | string, decimal | Valor do filme com ponto flutuante (100.000) |
> | id | int | |
> | :border.name |string | keywords.name, genres.name |
> | :border.iso_3166_1 |int | production_companies.iso_3166_1 |
> | :border.iso_3166_1 |int | production_countries.iso_3166_1 |
> | :border.id |int | keywords.id, genres.id |

Operadores

> | operator | description |
> | -------- | ----------- |
> | eq | Valor igual, para compos numericos e de texto |
> | gt | Valor maior que, para campos numericos |
> | lt | Valor menor que, para campos numericos |
> | lk | Valores que estão contidos na string |

### Exemplos
@todo