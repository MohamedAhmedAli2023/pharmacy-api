<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['id' => 1, 'name' => 'customer']);
        Role::create(['id' => 2, 'name' => 'pharmacist']);
        Role::create(['id' => 3, 'name' => 'admin']);
    }
}
