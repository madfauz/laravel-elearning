<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::firstOrCreate(
            [
                'username' => 'admin',
                'email' => 'admin@gmail.com',
            ],
            [
                'user_id' => uuid_create(),
                'name' => 'Admin',
                'password' => bcrypt('secret*123'),
            ],
        );
        $admin->assignRole('admin');

        $teacher = User::firstOrCreate(
            [
                'email' => 'mikalestari@gmail.com',
                'username' => 'mikalestari',
            ],
            [
                'user_id' => uuid_create(),
                'name' => 'Mika Lestari',
                'password' => bcrypt('secret*123'),
            ],
        );
        $teacher->assignRole('teacher');

        $students = ["budi", "intan", "restu", "vani", "cena", "pedro", "jose", "juni", "kemal"];

        foreach ($students as $student) {
            $student = User::firstOrCreate(
                [
                    'email' => $student . '@gmail.com',
                    'username' => $student,
                ],
                [
                    'user_id' => uuid_create(),
                    'name' => ucwords($student),
                    'password' => bcrypt('secret*123'),
                ],
            );
            $student->assignRole('student');
        }
    }
}
