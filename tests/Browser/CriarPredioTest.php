<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CriarPredioTest extends DuskTestCase
{
    /**
     * Teste de criação de prédios
     */
    use DatabaseTruncation; // zera banco de dados 
    public function test_criar_predio(): void
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
                ->assertPathIs('/predios');
            // Cria novo prédio
            $browser->clickLink('Adicionar Novo Prédio')
                ->waitForLocation('/predios/create', 5);

            // Preenche formulário
            $browser->type('nome', 'Prédio Teste Dusk')
                ->type('descricao', 'Descrição do prédio')
                ->waitFor('button[type="submit"]', 5)
                ->press('Salvar');

            // Verifica se o prédio foi criado
            $browser->waitForText('Prédio criado com sucesso!', 5)
                ->assertSee('Prédio Teste Dusk');
        });
    }
}
