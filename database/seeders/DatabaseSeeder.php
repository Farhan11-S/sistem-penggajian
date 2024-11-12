<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        if (User::where('email', 'admin@mail.com')->doesntExist()) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'admin@mail.com',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('admin');
        }
    }
}
