<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MediaFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaFolder>
 */
class MediaFolderFactory extends Factory
{
    protected $model = MediaFolder::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->optional()->sentence(),
            'parent_id' => null,
        ];
    }
}
