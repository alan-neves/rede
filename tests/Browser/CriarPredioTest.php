<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarPredioTest extends DuskTestCase
{
    /**
     * Teste de criação de prédios 
     */
     use DatabaseTruncation;

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
                ->assertPathIs('/predios');

            // Cria novo prédio
            $browser->clickLink('Adicionar Novo Prédio')
                ->assertPathIs('/predios/create');
            
            // Preenche formulário
            $browser->type('nome', 'Prédio Teste Dusk')
                ->type('descricao', 'Descrição do prédio')
                ->press('Salvar')
                ->pause('100');
            
            // Verifica se o prédio foi criado
            $browser->assertPathIs('/predios')
                ->assertSee('Prédio criado com sucesso!')
                ->assertSee('Prédio Teste Dusk');
        });
    }
}
