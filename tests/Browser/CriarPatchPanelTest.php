<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarPatchPanelTest extends DuskTestCase
{
    /**
     * Teste de criação de Patch Panel. 
     */
    public function test_criar_patch_panel(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://rede/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->waitForText('Sistema rede', 5);

            // Navega até o rack
            $browser->clickLink('Prédios')
                ->waitForLocation('/predios', 5)
                ->clickLink('Ver')
                ->waitFor('a[href="/racks/1"]', 5)
                ->click('a[href="/racks/1"]')
                ->waitForLocation('/racks/1', 5);

            // Clica em novo patch panel
            $browser->click('a[href="/patch-panels/create?rack_id=1"]')
                ->waitForLocation('/patch-panels/create', 5);

            // Preenche e salva
            $browser->type('nome', '1.0')
                ->type('qtde_portas', '24')
                ->waitFor('button[type="submit"]', 5)
                ->press('Salvar');

            // Aguarda redirect e mensagem de sucesso
            $browser->waitForText('Patch panel criado com sucesso!', 5)
                ->assertSee('1.0');
        });
    }
}
