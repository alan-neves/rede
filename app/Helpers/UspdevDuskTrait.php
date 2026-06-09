<?php

namespace App\Helpers;

use App\Models\User;
use Spatie\Permission\Models\Permission;

trait UspdevDuskTrait
{
    protected $adminUser;
    protected $commonUser;

    protected function setupAdminAndUser()
    {
        Permission::firstOrCreate(['name' => 'admin', 'guard_name' => 'senhaunica']);
        Permission::firstOrCreate(['name' => 'user', 'guard_name' => 'senhaunica']);

        $this->commonUser = User::firstOrCreate(
            ['email' => 'user@test.com'],
            ['name' => 'Dusk User', 'password' => bcrypt('password'), 'local' => 1]
        );
        $this->commonUser->givePermissionTo('user');

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Dusk Admin', 'password' => bcrypt('password'), 'local' => 1]
        );
        $this->adminUser->givePermissionTo('admin');
    }
}
