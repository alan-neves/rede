<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarSalaTest extends DuskTestCase
{
    /**
     * Teste de criação de salas
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
            
            // Clica no link para criar nova sala
            $browser->clickLink('Novo Local/Sala')
                ->assertPathIs('/salas/create');

            // Preenche formulário
            $browser->type('nome', 'Sala Teste 101')
                ->press('Salvar')
                ->pause('100');

            // Verifica se a sala foi criada
            $browser->assertSee('Sala criada com sucesso!')
                ->assertSee('Sala Teste 101');
        });
    }
}
