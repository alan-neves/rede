<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarRackTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_example(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://rede/callback')
                ->type('#loginUsuario', '1111')      
                ->press('Login')
                ->pause('100');

            // Vai diretamente para a lista de prédios
            $browser->clickLink('Prédios')
                ->assertPathIs('/predios')
                ->clickLink('Ver');
            
            // Clica no link para criar novo rack
            $browser->clickLink('Novo Rack')
                ->assertPathIs('/racks/create');
            
            // Preenche formulário
            $browser->type('nome', 'Rack A')
                ->press('Salvar')
                ->pause('100');

            // Verifica se o rack foi criado
            $browser->assertSee('Rack criado com sucesso!')
                ->assertSee('Rack A');
        });
    }
}
