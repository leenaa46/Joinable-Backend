<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Variable;

class FeedbackStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $feedbackStatues = [
            [
                "name" => "So Bad",
                "description" => "I really don't like this",
            ],
            [
                "name" => "Bad",
                "description" => "It looks to bad.",
            ],
            [
                "name" => "Better now",
                "description" => "But need a better than this",
            ],
            [
                "name" => "Good job",
                "description" => "Thanks you every ones!",
            ]
        ];

        foreach ($feedbackStatues as $i => $item) {
            Variable::create([
                "name" => $item["name"],
                "description" => $item["description"],
                "type" => 'feedback_status',
            ]);


            echo ("Seed Feedback Statues " . ($i + 1) . '/' . count($feedbackStatues) . "\n");
        }
    }
}
