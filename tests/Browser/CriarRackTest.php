<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarRackTest extends DuskTestCase
{
    /**
     * Teste de criação de rack
     */
    public function test__criar_rack(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://rede/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->waitForText('Rede', 5);
            // Vai diretamente para a lista de prédios
            $browser->clickLink('Prédios')
                ->waitForLocation('/predios', 5)
                ->clickLink('Ver')
                ->waitForText('Novo Rack');

            // Clica no link para criar novo rack
            $browser->clickLink('Novo Rack')
                ->waitForLocation('/racks/create', 5);

            // Preenche formulário
            $browser->type('nome', 'Rack A')
                ->waitFor('button[type="submit"]', 5)
                ->press('Salvar')
                ->pause('100');
            // Verifica se o rack foi criado
            $browser->waitForText('Rack criado com sucesso!', 5)
                ->assertSee('Rack A');
        });
    }
}
