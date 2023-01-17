<?php

namespace App\Services\Parsers;

use App\Enums\VolumeTypeEnum;
use App\Interfaces\CocktailParser;
use App\Models\Ingredient;
use App\Models\Recipe;
use DB;
use Illuminate\Support\Collection;
use Throwable;

class SvetalanaCocktailsParser implements CocktailParser
{
    public function uploadIngredients(Collection $existsIngredients): void
    {
        $ingredients = json_decode(
            file_get_contents(storage_path('cocktail-data/svetalana-cocktails/ingredients.json'))
        );

        foreach ($ingredients as $ingredientData) {
            $ingredient = $existsIngredients->fastSearch($ingredientData->ingredient, 'name');

            if (!$ingredient) {
                $ingredient = new Ingredient();
                $ingredient->name = $ingredientData->ingredient;

                $ingredient->save();

                $existsIngredients->push($ingredient);
            }
        }
    }

    /**
     * @throws Throwable
     */
    public function uploadCocktails(Collection $ingredients, Collection $existsRecipes): void
    {
        $recipes = json_decode(file_get_contents(storage_path('cocktail-data/svetalana-cocktails/cocktails.json')));

        foreach ($recipes as $recipeData) {
            DB::beginTransaction();

            $recipe = $existsRecipes->fastSearch($recipeData->name, 'name');

            if (!$recipe) {
                $recipe = new Recipe();
                $recipe->name = $recipeData->name;
                $existsRecipes->push($recipe);
            }

            $recipe->taste = $recipeData->taste;
            $recipe->save();

            foreach ($recipeData->ingredients as $ingredientData) {
                $existIngredient = $ingredients->fastSearch($ingredientData->ingredient, 'name');

                if (!$existIngredient) {
                    $existIngredient = new Ingredient();
                    $existIngredient->name = $ingredientData->ingredient;

                    try {
                        $existIngredient->save();
                    } catch (Throwable $exception) {
                        logger()
                            ->channel('cocktails-parser')
                            ->error($exception);
                        DB::rollBack();
                        break;
                    }

                    $ingredients->push($existIngredient);
                }

                if ($ingredientData->unit === 'tsp') {
                    $ingredientData->unit = VolumeTypeEnum::Teaspoon->value;
                }

                $recipe->ingredients()->syncWithPivotValues([$existIngredient->id], [
                    'volume' => $ingredientData->amount,
                    'volume_type' => VolumeTypeEnum::findFrom($ingredientData->unit)
                ]);
            }

            DB::commit();
        }
    }
}
