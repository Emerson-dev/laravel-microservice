<p align="center">
  <a href="http://nestjs.com/" target="blank"><img src="http://maratona.fullcycle.com.br/public/img/logo-maratona.png"/></a>
</p>

## Descrição

Microsserviço de catálogo

## Rodar a aplicação

#### Crie os containers com Docker

```bash
$ docker-compose up
```

```bash
$ docker-compose exec app bash
```

#### Accesse no browser

```
http://localhost:8000
```

## COMANDOS

docker-compose exec app bash

php artisan make:model Models/Category --all
php artisan make:seeder CategoriesTableSeeder
php artisan migrate --seed
php artisan tinker
\App\Models\Category::all()
php artisan migrate:refresh --seed
php artisan migrate:fresh --seed
php artisan route:list
php artisan make:request CategoryRequest


## TEST

vendor/bin/phpunit
vendor/bin/phpunit --filter CategoryTest
vendor/bin/phpunit --filter CategoryTest::testExample
php artisan make:test Models/CategoryTest --unit
php artisan make:test Models/CategoryTest


## GCP SEGURANCA

gcloud init

gcloud kms encrypt --ciphertext-file=./storage/credentials/google/laravel-microservice-a9c4ab9c3805.json.enc --plaintext-file=./storage/credentials/google/laravel-microservice-a9c4ab9c3805.json --location=global --keyring=testing --key=service-account
