<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarEquipamentoTest extends DuskTestCase
{
    /**
     * Teste de criação de switch/equipamento
     */
    public function test_criar_equipamento(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://rede/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->waitForText('Sistema rede', 5);

            // Vai para lista de prédios
            $browser->clickLink('Prédios')
                ->waitForLocation('/predios', 5)
                ->clickLink('Ver');

            // Entra no primeiro rack
            $browser->waitFor('a[href="/racks/1"]', 5)
                ->click('a[href="/racks/1"]')
                ->waitForLocation('/racks/1', 5);

            // Clica no botão "Novo" dos equipamentos
            $browser->click('a[href="/equipamentos/create?rack_id=1"]')
                ->waitForLocation('/equipamentos/create', 5);

            // Preenche formulário
            $browser->select('modelo_switch_id', '1')
                ->type('hostname', 'SW-TESTE-01')
                ->type('ip', '192.168.1.100')
                ->select('tipo', 'A')
                ->waitFor('button[type="submit"]', 5)
                ->press('Salvar');

            // Verifica se o equipamento foi criado
            $browser->waitForText('Equipamento criado com sucesso!', 5)
                ->assertSee('SW-TESTE-01');
        });
    }
}