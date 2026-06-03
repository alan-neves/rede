# Rede

Sistema para gerenciamento de infraestrutura de rede da FFLCH (prédios, salas, racks e equipamentos).

## Funcionalidades Principais

- **Gerenciamento de Prédios**
  - Cadastro, edição e remoção de prédios
  - Visualização da lista de prédios

- **Gerenciamento de Salas/Locais**
  - Associação de salas a prédios
  - Cadastro e edição de salas

- **Gerenciamento de Racks**
  - Cadastro de racks associados a salas
  - Organização de equipamentos por rack

- **Gerenciamento de Equipamentos**
  - Cadastro de equipamentos de rede

- **Autenticação**
  - Login via SenhaÚnica USP
  - Login local para testes (usuários administrador/comum)

## Tecnologias

- **Backend**: Laravel 12
- **Frontend**: Bootstrap 5, Blade Templates
- **Banco de Dados**: MariaDB / MySQL
- **Autenticação**: SenhaÚnica USP + Spatie Permission
- **Testes**: Laravel Dusk (testes de interface)

## Instalação

### 1. Clone o repositório

```
git clone LINK

cd rede
```

### 2. Build da imagem Docker

```
docker build --no-cache -t rede .
```

### 3. Configure o ambiente

```
cp .env.example .env
```

Edite o `.env` e ajuste as variáveis necessárias, especialmente:

```
DB_HOST=rede_mariadb          # nome do serviço no docker-compose
DB_DATABASE=rede
DB_USERNAME=rede
DB_PASSWORD=rede

SENHAUNICA_KEY=               # credenciais da Senha Única USP
SENHAUNICA_SECRET=
SENHAUNICA_CALLBACK_ID=
SENHAUNICA_ADMINS=            # número(s) USP dos administradores
```

### 4. Suba os containers

```
docker compose up -d
```

### 5. Gere a chave da aplicação

```
docker exec -it rede php artisan key:generate
```

### 6. Execute as migrations

```
docker exec -it rede php artisan migrate
```

A aplicação estará disponível em http://127.0.0.1:8000.

## Testes com Dusk

### 1. Crie o banco de dados de testes

```
docker compose exec rede_mariadb mariadb -u root -prede \
  -e "CREATE DATABASE IF NOT EXISTS rede_dusk;"

docker compose exec rede_mariadb mariadb -u root -prede \
  -e "GRANT ALL PRIVILEGES ON rede_dusk.* TO 'rede'@'%'; FLUSH PRIVILEGES;"
```

### 2. Configure o ambiente Dusk

```
cp .env.dusk.local.example .env.dusk.local
```

Edite o `.env.dusk.local` e cole o valor de `APP_KEY` do seu `.env`:

```
APP_KEY=    # copie do seu .env
```

Confirme também que a variável `DUSK_DRIVER_URL` aponta para o Selenium:

```
DUSK_DRIVER_URL=http://rede_selenium:4444
```

### 3. Execute as migrations no banco de testes

```
docker exec -it rede php artisan migrate --env=dusk.local
```

### 4. Execute os testes

```
docker exec -it rede php artisan dusk
```

### Novos testes

No diretório app/Helpers foi criado uma Trait UspdevDuskTrait.php que criar um usuário administrador e outro usuário como a partir da biblioteca senhaunica-socialite, assim em novos testes carregar essa Trait:

    use App\Helpers\UspdevDuskTrait;
    class NovoTest  extends DuskTestCase{
        use UspdevDuskTrait;

        protected function setUp(): void
        {
            parent::setUp();
            ...
            $this->setupAdminAndUser(); // cria usuários $this->commonUser e $this->adminUser
        }

    ...

## API
Exemplo de requisição post com shell:

```bash
curl --include --header "Authorization: senha-ultra-secreta" \
-X POST -H "Accept: aplication/json" -H "Content-Type: application/json" --data \
'{"hostname": "008.054517","ip": "200.0.1.4","poe_type": true,"model": "hp_comware","qtde_portas": 24,"rack_id": 1,"user_id": 1}' \
http://127.0.0.1:8000/api/equipamentos
```
## Equipe de Desenvolvimento:

[Alan Neves](https://github.com/alan-neves)  
[Ricardo Fontura](https://github.com/ricardfo)  
[Thiago G. Verissimo](https://github.com/thiagogomesverissimo)