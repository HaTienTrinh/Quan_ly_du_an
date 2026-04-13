<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $brands = [
            'Nike' => [
                'Air Max 90', 'Air Max 270', 'Air Force 1', 'Air Zoom Pegasus 40',
                'React Infinity Run', 'Dunk Low', 'Dunk High', 'Air Jordan 1 Low',
                'Air Jordan 1 Mid', 'Air Jordan 4', 'Blazer Mid 77', 'Free Run 5.0',
            ],
            'Adidas' => [
                'Ultraboost 23', 'Stan Smith', 'Superstar', 'NMD R1',
                'Gazelle', 'Samba OG', 'Forum Low', 'Forum Mid',
                'Ozweego', 'Adizero Boston 12', 'Response CL', 'Campus 00s',
            ],
            'Puma' => [
                'Suede Classic', 'RS-X', 'Clyde All-Pro', 'Velocity Nitro 2',
                'Deviate Nitro 2', 'Future Rider', 'Cali Dream', 'Mayze Stack',
            ],
            'New Balance' => [
                '574 Core', '990v6', '1080v13', '530',
                '2002R', '327', '9060', 'Fresh Foam X 1080',
            ],
            'Converse' => [
                'Chuck Taylor All Star Low', 'Chuck Taylor All Star High',
                'Chuck 70 Low', 'Chuck 70 High', 'Run Star Hike',
                'One Star Pro', 'Pro Leather Low', 'Pro Leather High',
            ],
        ];

        $brand = $this->faker->randomElement(array_keys($brands));
        $model = $this->faker->randomElement($brands[$brand]);
        $name = $brand . ' ' . $model;

        $categoryId = Category::where('name', $brand)->value('id')
            ?? $this->faker->numberBetween(1, 5);

        $price = $this->faker->randomElement([890000, 990000, 1190000, 1390000, 1590000, 1890000, 2190000, 2490000, 2990000]);
        $hasSale = $this->faker->boolean(35);
        $salePrice = $hasSale ? (int) ($price * $this->faker->randomElement([0.7, 0.75, 0.8, 0.85])) : null;

        return [
            'category_id' => $categoryId,
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'description' => "Giày {$brand} {$model} - thiết kế hiện đại, đế đệm êm ái, phù hợp cho cả thể thao lẫn đời thường.",
            'price'       => $price,
            'sale_price'  => $salePrice,
            'stock'       => $this->faker->numberBetween(10, 80),
            'thumbnail'   => null,
            'is_active'   => $this->faker->boolean(85),
        ];
    }
}
