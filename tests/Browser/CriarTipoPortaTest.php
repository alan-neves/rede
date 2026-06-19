<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarTipoPortaTest extends DuskTestCase
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

            // Vai diretamente para a lista de tipo de portas
            $browser->clickLink('Tipos de Porta')
                ->assertPathIs('/tipo-portas');

            // Cria novo tipo de porta
            $browser->clickLink('Novo Tipo')
                ->assertPathIs('/tipo-portas/create');
            
            // Preenche formulário
            $browser->type('nome', 'Voip')
                ->press('Salvar')
                ->pause('100');
            
            // Verifica se o tipo de porta foi criado
            $browser->assertPathIs('/tipo-portas')
                ->assertSee('Tipo de porta criado com sucesso!')
                ->assertSee('Voip');
        });
    }
}
