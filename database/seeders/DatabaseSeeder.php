<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

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
            RoleSeeder::class,
        ]);

        $company = Company::create([
            'joinable_code' => Str::random(7)
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => \bcrypt('11111111')
        ])->assignRole('admin');
    }
}
