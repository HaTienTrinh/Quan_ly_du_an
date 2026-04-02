<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $price = $this->faker->numberBetween(500000, 3000000);

        return [
            'category_id' => $this->faker->numberBetween(1, 5),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->sentence(10),
            'price' => $price,
            'sale_price' => $this->faker->optional(0.3)->numberBetween($price - 500000, $price - 100000),
            'stock' => $this->faker->numberBetween(5, 100),
            'thumbnail' => $this->faker->optional(0.7)->imageUrl(400, 400, 'shoes'),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
