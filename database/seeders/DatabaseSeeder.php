<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Traits\ImageTrait;
use App\Services\Post\PostService;
use App\Models\Variable;
use App\Models\User;
use App\Models\Post;
use App\Models\Personal;
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
            FeedbackStatusSeeder::class,
        ]);

        $companies = 1;
        foreach (\range(1, $companies) as $i) {
            $company = Company::create([
                'joinable_code' => "JOIN$i",
                'name' => "Joinable $i",
                "slogan" => "Innovate Business Solutions."
            ]);
            $this->addToModel("https://source.unsplash.com/random/400x400/?brand", $company, $company->image_profile_collection_name);

            $admin = User::create([
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
                "created_by" => $admin->id
            ]));

            $employees = 20;
            $personalIds = [];
            foreach (\range(1, $employees) as $e => $emp) {
                $user = User::create([
                    'company_id' => $company->id,
                    'name' => "emp_$company->id$e",
                    'email' => "emp_$company->id$e@example.com",
                    'password' => \bcrypt('11111111')
                ])->assignRole('employee');

                $personal = Personal::create([
                    "name" => "emp_$company->id$e",
                    "user_id" => $user->id
                ]);
                $personalIds[] = $personal->id;

                $career = Variable::where('type', 'career')->inRandomOrder()->first();
                $personal->variables()->attach($career);
                $activities =  Variable::where('type', 'activity')->inRandomOrder()->take(\random_int(3, 5))->pluck('id')->toArray();
                $personal->variables()->syncWithoutDetaching($activities);

                echo ("Seed Employee $e/$employees \n");
            }

            $events = 20;
            foreach (\range(0, $events) as $eIndex => $event) {
                $post = Post::create([
                    "created_by" => Personal::whereIn('id', $personalIds)->inRandomOrder()->first()->user_id,
                    "type" => "event",
                    "title" => Str::random(\random_int(20, 50)),
                    "body" => Str::random(\random_int(120, 500)),
                    "is_published" => true,
                    "schedule" => \date('Y-m-d H:i:s', rand(\strtotime(Carbon::now()->startOfMonth()), \strtotime(Carbon::now()->endOfMonth()))),
                ]);
                $activity =  Variable::where('type', 'activity')->inRandomOrder()->first()->id;
                $post->activities()->attach($activity);

                Post::create([
                    "created_by" => $admin->id,
                    "type" => "faq",
                    "title" => Str::random(\random_int(20, 50)),
                    "body" => Str::random(\random_int(120, 500)),
                    "is_published" => true,
                ]);

                echo ("Seed Event $eIndex/$event \n");
            }

            echo ("Seed Company $i/$companies \n");
        }
    }
}
