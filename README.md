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

Gerar a documentação da api se necessário com o comando:
```bash
php artisan movieapp:swagger
```

Configurar se necessário o nginx:

```conf

server {

    # outras configurações

    location /swagger/ {
        root /projects/movieapp/public;  # Ajuste para o diretório correto do Swagger
        index index.html;  # Define o arquivo principal
    }

}

```
Url da documentação do swagger:
http://dev.backend.com/swagger/index.html

Url do json swagger:
http://dev.backend.com/swagger/swagger.json
