<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Traits\ImageTrait;
use App\Models\Variable;

class ActivityCareerSeeder extends Seeder
{
    use ImageTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $activities = [
                [
                    "name" => "Gaming",
                    "description" => "We Are Gamer",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/1374/1374723.png"
                ],
                [
                    "name" => "Sports",
                    "description" => "We Like Sports",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/857/857418.png"
                ],
                [
                    "name" => "Gym",
                    "description" => "Lets go to the Gym",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/1198/1198314.png"
                ],
                [
                    "name" => "Songs",
                    "description" => "Songs Listeners",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/3208/3208679.png"
                ],
                [
                    "name" => "Anime",
                    "description" => "For Who like Anime",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/8669/8669821.png"
                ],
                [
                    "name" => "Super Hero",
                    "description" => "Super Hero",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/892/892721.png"
                ]
            ];

            foreach ($activities as $i => $activity) {
                $item = Variable::create([
                    "name" => $activity["name"],
                    "description" => $activity["description"],
                    "type" => 'activity'
                ]);

                $this->addToModel($activity['image_logo'], $item, $item->image_logo_collection_name, $activity);

                echo ("Seed Activity " . ($i + 1) . '/' . count($activities) . "\n");
            }

            $careers = [
                [
                    "name" => "CEO",
                    "description" => "Chief Executive Officer",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/4961/4961733.png"
                ],
                [
                    "name" => "Account",
                    "description" => "Account",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/2942/2942269.png"
                ],
                [
                    "name" => "HR",
                    "description" => "Human Resource Manager",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/4126/4126442.png"
                ],
                [
                    "name" => "Marketing",
                    "description" => "Marketing Designer",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/2518/2518048.png"
                ],
                [
                    "name" => "IT",
                    "description" => "IT Engineer",
                    "image_logo" => "https://cdn-icons-png.flaticon.com/512/1055/1055683.png"
                ]
            ];

            foreach ($careers as $i => $career) {
                $item = Variable::create([
                    "name" => $career["name"],
                    "description" => $career["description"],
                    "type" => 'career'
                ]);

                $this->addToModel($career['image_logo'], $item, $item->image_logo_collection_name, $activity);

                echo ("Seed Career " . ($i + 1) . '/' . count($careers) . "\n");
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
