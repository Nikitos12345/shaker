<?php

namespace Tests\Feature;

use App\Models\Pivot\IngredientRecipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function testGetRecipesWithIngredients(): void
    {
        IngredientRecipe::factory()->count(10)->create();

        $response = $this->getJson('/api/recipes');

        $response->assertOk()->assertJsonCount(10, 'data');
    }
}
