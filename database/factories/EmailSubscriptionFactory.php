<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailSubscription>
 */
class EmailSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->optional()->name(),
            'wants_news' => $this->faker->boolean(85),
            'wants_events' => $this->faker->boolean(85),
            'wants_announcements' => $this->faker->boolean(85),
            'wants_scholarships' => $this->faker->boolean(85),
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
            'last_notified_at' => null,
            'meta' => null,
            'unsubscribe_token' => Str::random(64),
        ];
    }
}
