<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'picture' => 'https://i.pravatar.cc/300?img=0',
                'firstname' => 'Brent',
                'middlename' => '',
                'lastname' => 'Pabua',
                'email' => 'admin@gmail.com',
                'phone' => '+639123456781',
                'region' => 'Region X',
                'province' => 'Misamis Oriental',
                'zip_code' => '9000',
                'street' => 'Lapasan',            
                'address' => 'House 1',
                'password' => bcrypt('admin'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('users')->insert($users);
    }
}
