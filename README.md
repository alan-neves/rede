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

### Requisitos

- PHP 8.1+
- Composer
- MariaDB / MySQL
- Git
- Chrome/Chromium (para testes Dusk)

### Configuração do Ambiente de Desenvolvimento
Gerar chave:
```php
php artisan key:generate
```

Configurar .env:
```php
cp .env.example .env
```

Instalar dependências:
```php
composer install
```

Executar migrações:
```php
php artisan migrate
```

## Rodando testes com Dusk (testes de cobertura)

Os testes com **Laravel Dusk** foram desenvolvidos com dois propósitos:

1. **Testar funcionalidades reais do sistema**, simulando a interação de um usuário no navegador.
2. **Servir como documentação funcional**, demonstrando como as principais funcionalidades do sistema devem se comportar.

A seguir estão os passos para executar o Dusk **em modo assistido**, ou seja, diretamente na sua máquina. Nesse modo é possível **visualizar o navegador Chrome virtual executando os testes**, o que facilita a depuração.

Por esse motivo, neste caso **não executamos os testes em container**.

Entretanto, na pasta **`.github/workflows`** os testes também foram configurados para rodar automaticamente no **GitHub Actions**, garantindo que falhas nos testes sejam detectadas durante novos commits ou pull requests.


### 1. Criar o arquivo de ambiente de testes

Copie o arquivo de exemplo:

    cp .env.testing.example .env.testing

### 2. Criar o arquivo de ambiente de testes

Edite o arquivo .env.testing e configure pelo menos as variáveis de banco de dados:

    DB_DATABASE=salas_dusk
    DB_USERNAME=admin
    DB_PASSWORD=admin

### 3. Configurar a porta da aplicação

Os testes serão executados na porta 47800. Caso prefira utilizar outra porta, basta alterar o valor de APP_URL.


### 4. Preparar o ambiente de testes

    composer install
    php artisan key:generate --env=testing
    php artisan migrate:fresh --env=testing
    php artisan serve --port=47800 --env=testing

### 5. Executar os testes do Dusk

Durante a execução, o navegador Chrome controlado pelo Laravel Dusk abrirá automaticamente e realizará as interações definidas nos testes.

    php artisan dusk --env=testing

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