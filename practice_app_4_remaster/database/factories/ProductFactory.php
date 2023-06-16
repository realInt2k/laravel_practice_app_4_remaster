<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        $randomNumber = rand(1, 6);
        $userIds = User::pluck('id')->toArray();
        if (User::count() > 0) {
            $userId = $userIds[array_rand($userIds)];
        } else {
            $userId = User::factory()->create()->id;
        }
        $categoryIds = Category::pluck('id')->toArray();
        if (Category::count() > 0) {
            $categoryId = $categoryIds[array_rand($categoryIds)];
        } else {
            $categoryId = Category::factory()->create()->id;
        }
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'user_id' => $userId,
        ];
    }
}
