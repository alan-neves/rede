<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarPatchPanelTest extends DuskTestCase
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
                ->press('Login');

            // Vai diretamente para a lista de prédios
            $browser->clickLink('Prédios')
                ->assertPathIs('/predios')
                ->clickLink('Ver');

            // Vai para lista de patch panel
            $browser->click('a[href="/racks/1"]')
                ->assertPathIs('/racks/1');

            // Clica no link para criar novo patch panel
            $browser->click('a[href="/patch-panels/create?rack_id=1"]')
                ->assertPathIs('/patch-panels/create');
            
            // Preenche formulário
            $browser->type('nome', '1.0')
                ->type('qtde_portas', '24')
                ->press('Salvar');

            // Verifica se o patch panel foi criada
            $browser->assertSee('Patch panel criado com sucesso!')
                ->assertSee('1.0');
        });
    }
}
