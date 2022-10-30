<?php

namespace Database\Factories\Pivot;

use App\Enums\VolumeTypeEnum;
use App\Models\Ingredient;
use App\Models\Pivot\IngredientRecipe;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientRecipeFactory extends Factory
{
    protected $model = IngredientRecipe::class;

    public function definition(): array
    {
        return [
            'ingredient_id' => Ingredient::factory(),
            'recipe_id' => Recipe::factory(),
            'volume' => $this->faker->numberBetween(int2: 100),
            'volume_type' => $this->faker->randomElement(VolumeTypeEnum::cases()),
        ];
    }
}
