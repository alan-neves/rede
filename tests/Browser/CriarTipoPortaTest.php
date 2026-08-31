<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarTipoPortaTest extends DuskTestCase
{
    /**
     * Teste de criação de tipo de porta
     */
    public function test_criar_tipo_porta(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://rede/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->waitForText('Sistema rede', 5);
            // Vai diretamente para a lista de tipo de portas
            $browser->clickLink('Tipos de Porta')
                ->waitForLocation('/tipo-portas', 5);
            // Cria novo tipo de porta
            $browser->clickLink('Novo Tipo')
                ->waitForLocation('/tipo-portas/create', 5);

            // Preenche formulário
            $browser->type('nome', 'Voip')
                ->waitFor('button[type="submit"]', 5)
                ->press('Salvar');

            // Verifica se o tipo de porta foi criado
            $browser->waitForText('Tipo de porta criado com sucesso!', 5)
                ->assertSee('Voip');
        });
    }
}
