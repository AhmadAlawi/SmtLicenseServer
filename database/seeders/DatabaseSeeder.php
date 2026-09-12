<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First SMTGROUP staff login for the platform dashboard. Change the
        // password immediately after first login — this seeder is meant
        // for initial local/staging setup, not a production credential.
        User::factory()->create([
            'name'     => 'SMTGROUP Admin',
            'email'    => 'ahmad.alalawi@smt.com.jo',
            'password' => bcrypt('change-me-now'),
        ]);

        $this->call(PlansSeeder::class);
        $this->call(BlogPostsSeeder::class);
    }
}
