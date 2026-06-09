<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Helpers\UspdevDuskTrait;

class CriarPredioSalaRackTest extends DuskTestCase
{
    use UspdevDuskTrait;
    use DatabaseTruncation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupAdminAndUser();
    }

    public function testCriarPredioSalaRack()
    {
        $this->browse(function (Browser $browser) {
                // Login como admin
                $browser->visit('/loginlocal')
                    ->typeSlowly('email', $this->adminUser->email)
                    ->typeSlowly('password', 'password')
                    ->press('Entrar')
                    ->pause(2000);

                // Vai diretamente para a lista de prédios
                $browser->clickLink('Prédios')
                        ->pause(500)
                        ->assertPathIs('/predios');

                // Cria novo prédio
                $browser->clickLink('Adicionar Novo Prédio')
                        ->pause(500)
                        ->assertPathIs('/predios/create');

                $browser->type('nome', 'Prédio Teste Dusk')
                        ->type('descricao', 'Descrição do prédio')
                        ->press('Salvar')
                        ->pause(1000);

                $browser->assertPathIs('/predios')
                        ->assertSee('Prédio criado com sucesso!')
                        ->assertSee('Prédio Teste Dusk');

                // Acessa página do prédio criado
                $browser->clickLink('Ver')
                        ->pause(500);

                // Cria nova sala
                $browser->clickLink('Novo Local/Sala')
                        ->pause(500)
                        ->assertPathIs('/salas/create');

                $browser->type('nome', 'Sala Teste 101')
                        ->press('Salvar')
                        ->pause(1000);

                $browser->assertSee('Sala criada com sucesso!')
                        ->assertSee('Sala Teste 101');

                // Cria novo rack
                $browser->clickLink('Novo Rack')
                        ->pause(500)
                        ->assertPathIs('/racks/create');

                $browser->type('nome', 'Rack A')
                        ->press('Salvar')
                        ->pause(1000);

                $browser->assertSee('Rack criado com sucesso!')
                        ->assertSee('Rack A');

                // Verificações finais
                $browser->assertSee('Sala Teste 101')
                        ->assertSee('Rack A');
        });
    }
}