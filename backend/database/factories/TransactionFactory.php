<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 10, 500);
        $platformFeeRate = 0.30; // 30% platform fee
        $platformFee = $amount * $platformFeeRate;
        $instructorRevenue = $amount - $platformFee;

        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'amount' => $amount,
            'currency' => 'THB',
            'platform_fee' => $platformFee,
            'instructor_revenue' => $instructorRevenue,
            'payment_method' => 'stripe',
            'payment_gateway_id' => 'ch_' . fake()->unique()->regexify('[a-zA-Z0-9]{24}'),
            'status' => fake()->randomElement(['pending', 'completed', 'refunded']),
            'refunded_at' => null,
            'refund_reason' => null,
        ];
    }

    /**
     * Indicate that the transaction is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'refunded_at' => now(),
            'refund_reason' => fake()->sentence(),
        ]);
    }
}

