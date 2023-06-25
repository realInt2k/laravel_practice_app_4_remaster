<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{

    public function withRandomPhoto(): ProductFactory
    {
        return $this->afterCreating(function (Product $product) {
            $faker = FakerFactory::create();
            $name = 'cat_' . time() . '.png';
            $fakeImageUrl = $faker->imageUrl(640, 480, 'animals', true);
            $file = file_get_contents($fakeImageUrl);
            Storage::disk('public')->put('images/' . $name, $file);
            $product->update(['image' => $name]);
        });
    }

    public function withRandomCategory(): ProductFactory
    {
        return $this->afterCreating(function (Product $product) {
            $category = Category::factory()->create();
            $product->syncCategories([$category->id]);
        });
    }

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
