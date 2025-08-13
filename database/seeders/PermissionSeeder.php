<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    protected $permissions = [
        'web' => [
            'Listar' => [
                'Cliente',
                'Domínio',
                'Hospedagem',
                'Provedor de Hospedagem',
                'Usuário'
            ],
            'Editar' => [
                'Cliente',
                'Domínio',
                'Hospedagem',
                'Provedor de Hospedagem',
                'Usuário',
                'Configuração'
            ],
            'Excluir' => [
                'Cliente',
                'Domínio',
                'Hospedagem',
                'Provedor de Hospedagem',
                'Usuário'
            ],
            'Ver' => [
                'Relatório de Status',
                'Painel de Controle',
                'Credenciais'
            ]
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $guard => $permissions) {
            foreach ($permissions as $action => $models) {
                foreach ($models as $model) {
                    try {
                        Permission::create([
                            'name' => $action . ' ' . $model,
                            'guard_name' => $guard
                        ]);
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }
        }
    }
}
