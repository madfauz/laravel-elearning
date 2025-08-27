<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Task::firstOrCreate(
            [
                "title" => "Tugas 1",
            ],
            [
                "task_id" => uuid_create(),
                "content" => "Kerjakan halaman 70 buku paket",
            ]
        );
    }
}
