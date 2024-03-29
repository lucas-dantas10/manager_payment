<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Transaction::class;
     
    public function definition(): array
    {
        return [
            'user_id' => User::all()->first()->id,
            'type' => 'expense',
            'amount' => fake()->numberBetween(1, 20),
            'description' => 'testes',
            'date_transaction' => now(),
            'created_by' => 1,
        ];
    }
}
