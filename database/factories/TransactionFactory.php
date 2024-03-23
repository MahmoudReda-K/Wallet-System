<?php

namespace Database\Factories;

use App\Constants\TransactionStatus;
use App\Models\Wallet;
use App\Constants\TransactionTypes;
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
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory()->create()->id,
            'type' => $this->faker->randomElement(TransactionTypes::getList()),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'transaction_fee' => 0,
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(TransactionStatus::getList()),
        ];
    }
}
