<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'happened_on' => $this->faker->dateTimeBetween('-50 days', 'now')->format('Y-m-d'),
            'description' => implode(' ', array_map('ucfirst', $this->faker->words(3))),
            'amount' => $this->faker->randomNumber(3)
        ];
    }
}
