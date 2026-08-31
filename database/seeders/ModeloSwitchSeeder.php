<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModeloSwitch;

class ModeloSwitchSeeder extends Seeder
{
    public function run(): void
    {
        $modelos = [
            ['nome' => '1920S 24G', 'fabricante' => 'HP', 'qtde_portas' => 24, 'qtde_portas_poe' => 12],
            ['nome' => '1920S 48G', 'fabricante' => 'HP', 'qtde_portas' => 48, 'qtde_portas_poe' => 24],
            ['nome' => 'Catalyst 2960', 'fabricante' => 'Cisco', 'qtde_portas' => 24, 'qtde_portas_poe' => 24],
            ['nome' => 'Catalyst 3750', 'fabricante' => 'Cisco', 'qtde_portas' => 48, 'qtde_portas_poe' => 48],
            ['nome' => '2530-24G', 'fabricante' => 'Aruba', 'qtde_portas' => 24, 'qtde_portas_poe' => 0],
            ['nome' => '2930F 48G', 'fabricante' => 'Aruba', 'qtde_portas' => 48, 'qtde_portas_poe' => 48],
        ];

        foreach ($modelos as $m) {
            ModeloSwitch::create($m + ['user_id' => 1]);
        }
    }
}