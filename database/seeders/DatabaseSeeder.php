<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use App\Traits\ImageTrait;
use App\Services\Post\PostService;
use App\Models\User;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    use ImageTrait;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            ActivityCareerSeeder::class,
        ]);

        $companies = 5;
        foreach (\range(1, $companies) as $i) {
            $company = Company::create([
                'joinable_code' => "JOIN$i",
                'name' => "Joinable $i",
                "slogan" => "Innovate Business Solutions."
            ]);
            $this->addToModel("https://source.unsplash.com/random/400x400/?brand", $company, $company->image_profile_collection_name);

            $user = User::create([
                'company_id' => $company->id,
                'name' => "joinable_$i",
                'email' => "joinable_$i@example.com",
                'password' => \bcrypt('11111111')
            ])->assignRole('admin');

            $postService = \resolve(PostService::class);

            $postService->create(new Request([
                "type" => "company_content",
                "title" => 'default',
                "body" => "default",
                "created_by" => $user->id
            ]));

            echo ("Seed Company $i/$companies \n");
        }
    }
}
