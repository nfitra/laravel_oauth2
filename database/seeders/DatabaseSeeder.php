<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//         \App\Models\User::factory(10)->create();

         User::factory()->create([
             'name' => '123',
             'email' => '123@123.123',
             'password' => '123',
         ]);

//        $user = User::find(12);
//        $token = $user->createToken('123 Personal Token', ['test1'])->accessToken;
//        $token = $user->createToken('Token Name')->accessToken;
    }
}
