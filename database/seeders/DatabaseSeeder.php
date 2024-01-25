<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => config('app.admin.name'),
            'email' => config('app.admin.email'),
            'password' => config('app.admin.password'),
            'is_admin' => true,
        ]);

        Balance::create([
            'user_id'=> $user->id,
            'total_amount' => 0.00,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'created_at' => now(),
        ]);

        Transaction::factory()->count(30)->create();
    }
}
