<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         for ($i=0; $i <= 3 ; $i++) { 
            User::create([
              'username' => "usuario0$i",
              'email' => "usuario0$i@gmail.com",
              'password' => bcrypt('Z123pasS'),
              'email_verified_at' => Carbon::now(),
              'active' => true,

            ]);
         }
    }
}
