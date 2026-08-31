<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            EquipamentoSeeder::class,
            PortaSeeder::class,
            SnapshotSeeder::class,
            MacSeeder::class,
            ModeloSwitchSeeder::class,
        ]);
    }
}
