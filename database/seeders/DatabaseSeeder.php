<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\Status;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
        Configuration::updateOrCreate(['id'=>1],[]);
        Status::updateOrCreate(['name' => 'Informado ao financeiro'], []);
        Status::updateOrCreate(['name' => 'Cobrança enviada'], []);
        Status::updateOrCreate(['name' => 'Aguardando Pagamento'], []);
        Status::updateOrCreate(['name' => 'Pago'], []);
        Status::updateOrCreate(['name' => 'Não Renovar'], []);
    }
}
