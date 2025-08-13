<?php

namespace Database\Seeders;

use App\Models\RoleHasPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    protected $roles = [
        'web' => [
            'Cliente',
            'Domínio',
            'Hospedagem',
            'Provedor de Hospedagem',
            'Usuário',
            'Configuração',
            'Relatório de Status',
            'Painel de Controle',
        ]
    ];

    protected $permissions = [
        'Listar',
        'Editar',
        'Excluir'
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            [
                'name' => 'Super Admin',
                'guard_name' => 'web'
            ]
        );
        foreach ($this->roles as $guard => $roles) {
            foreach($roles as $role){
                try {
                    $newRole = new Role();
                    $newRole->name = $role;
                    $newRole->guard_name = $guard;
                    $newRole->save();
                    foreach ($this->permissions as $permissions){
                        $permission = Permission::where('name', $permissions . ' ' . $role)->pluck('id')->first();
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $permission,
                            'role_id' => $newRole->id,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }
        foreach($this->permissions as $permission){
            try {
                $newRole = new Role();
                $newRole->name = $permission;
                $newRole->guard_name = 'web';
                $newRole->save();
                foreach($this->roles as $role){
                    $permission = Permission::where('name', $permission . ' ' . $role)->pluck('id')->first();
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permission,
                        'role_id' => $newRole->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
