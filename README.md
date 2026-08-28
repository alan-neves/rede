### Instalação Rápida

Configure o ambiente

    cp .env.example .env # coloque http://127.0.0.1:8000 no APP_URL
    docker compose up --build


criar key:

    docker exec rede php artisan key:generate

Rode as migrations:

    docker exec rede php artisan migrate


Acesse http://127.0.0.1:8000