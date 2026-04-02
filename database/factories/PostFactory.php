<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(5);

        return [
            'author_id' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? 1,
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'summary' => $this->faker->sentence(15),
            'content' => $this->faker->paragraphs(3, true),
            'thumbnail' => $this->faker->optional(0.7)->imageUrl(600, 400, 'shoes'),
            'status' => $this->faker->randomElement(['published', 'draft']),
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * State to create a published post
     */
    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'published',
                'published_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            ];
        });
    }

    /**
     * State to create a draft post
     */
    public function draft(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'published_at' => null,
            ];
        });
    }
}
