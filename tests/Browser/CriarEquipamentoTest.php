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
                ->pause('100');

            // Vai para lista de prédios
            $browser->clickLink('Prédios')
                ->assertPathIs('/predios')
                ->clickLink('Ver');

            // Entra no primeiro rack
            $browser->click('a[href="/racks/1"]')
                ->assertPathIs('/racks/1');

            // Clica no botão "Novo" dos equipamentos (seletor CSS pelo href)
            $browser->click('a[href="/equipamentos/create?rack_id=1"]')
                ->assertPathIs('/equipamentos/create');

            // Preenche formulário
            $browser->select('modelo_switch_id', '1')
                ->type('hostname', 'SW-TESTE-01')
                ->type('ip', '192.168.1.100')
                ->select('tipo', 'A')
                ->press('Salvar')
                ->pause('100');

            // Verifica se o equipamento foi criado
            $browser->assertSee('Equipamento criado com sucesso!')
                ->assertSee('SW-TESTE-01');
        });
    }
}