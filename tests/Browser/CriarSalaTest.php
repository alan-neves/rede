<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarSalaTest extends DuskTestCase
{
    /**
     * Teste de criação de sala.
     */
    public function test_criar_sala(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://rede/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->waitForText('Sistema rede', 5);
            // Vai diretamente para a lista de prédios
            $browser->clickLink('Prédios')
                ->waitForLocation('/predios', 5)
                ->clickLink('Ver')
                ->waitFor('a[href="/salas/create?predio_id=1"]', 5);

            // Clica no link para criar nova sala
            $browser->clickLink('Novo Local/Sala')
                ->waitForLocation('/salas/create', 5);
            // Preenche formulário
            $browser->type('nome', 'Sala Teste 101')
                ->waitFor('button[type="submit"]', 5)
                ->press('Salvar')
                ->pause('100');
            // Verifica se a sala foi criada
            $browser->waitForText('Sala criada com sucesso!', 5)
                ->assertSee('Sala Teste 101');
        });
    }
}
