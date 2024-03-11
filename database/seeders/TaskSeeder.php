<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    
    public function run(): void
    {
        $faker = Faker::create();
        $task = new Task;

        foreach (range(1, 10) as $index) {
            Task::create([
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'priority' => $faker->numberBetween(1, 5),
                'due_date' => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
                'completed' => $faker->boolean,
            ]);
        }
    }
}
