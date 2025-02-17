<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Kripal Shrestha',
            'email' => 'kripal@gmail.com',
            'password' => bcrypt('12345678'),
            'username' => 'kripalshr',
            'phone' => '987766551',
        ]);

        $user->assignRole('admin');

    }
}
