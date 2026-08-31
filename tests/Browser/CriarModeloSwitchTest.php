<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarModeloSwitchTest extends DuskTestCase
{
    /**
     * Teste de criação de modelo de switch/equipamento
     */
    public function test_criar_modelo_siwtch(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://rede/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->pause('100');

            // Vai para lista de modelos de switch
            $browser->clickLink('Modelos de Switch')
                ->assertPathIs('/modelo-switches');

            // Cria novo modelo
            $browser->clickLink('Novo Modelo')
                ->assertPathIs('/modelo-switches/create');

            // Preenche formulário
            $browser->type('nome', 'Switch Teste 24G')
                ->type('fabricante', 'HP')
                ->type('qtde_portas', '24')
                ->type('qtde_portas_poe', '12')
                ->press('Salvar')
                ->pause('100');

            // Verifica se o modelo foi criado
            $browser->assertPathIs('/modelo-switches')
                ->assertSee('Modelo cadastrado com sucesso!')
                ->assertSee('Switch Teste 24G');
        });
    }
}
