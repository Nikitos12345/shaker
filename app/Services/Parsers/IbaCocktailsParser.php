<?php

namespace App\Services\Parsers;

use App\Enums\VolumeTypeEnum;
use App\Interfaces\CocktailParser;
use App\Models\Ingredient;
use App\Models\Recipe;
use DB;
use Exception;
use Illuminate\Support\Collection;
use stdClass;
use Throwable;

class IbaCocktailsParser implements CocktailParser
{
    public function uploadIngredients(Collection $existsIngredients): void
    {
        $ingredients = json_decode(file_get_contents(storage_path('cocktail-data/iba-cocktails/ingredients.json')));

        foreach ($ingredients as $name => $ingredientData) {
            $ingredient = $existsIngredients->fastSearch($name, 'name');
            if (!$ingredient) {
                $ingredient = new Ingredient();
                $ingredient->name = $name;

                $existsIngredients->push($ingredient);
            }

            $ingredient->strength = $ingredientData->abv;
            $ingredient->taste = $ingredientData->taste;

            $ingredient->save();
        }
    }

    /**
     * @throws Throwable
     */
    public function uploadCocktails(Collection $ingredients, Collection $existsRecipes): void
    {
        $recipes = json_decode(file_get_contents(storage_path('cocktail-data/iba-cocktails/recipes.json')));

        foreach ($recipes as $recipeData) {
            DB::beginTransaction();

            $recipe = $existsRecipes->fastSearch($recipeData->name, 'name');
            $isNewRecipe = false;

            if (!$recipe) {
                $recipe = new Recipe();
                $recipe->name = $recipeData->name;
                $isNewRecipe = true;
            }

            $recipe->glass = $recipeData->glass;
            $recipe->category = $recipeData->category ?? null;
            $recipe->preparation = $recipeData->preparation ?? null;

            $recipe->save();

            if (!$isNewRecipe) {
                continue;
            }
            foreach ($recipeData->ingredients as $ingredientData) {
                if (isset($ingredientData->special)) {
                    try {
                        $ingredientData = $this->parseIbaSpecialIngredient($ingredientData->special);
                    } catch (Exception $exception) {
                        logger()
                            ->channel('cocktails-parser')
                            ->error("Can't parse data", json_decode(json_encode($ingredientData), true));
                        DB::rollBack();
                        break;
                    }
                }

                $existIngredient = $ingredients->fastSearch($ingredientData->ingredient, 'name');

                if (!$existIngredient && isset($ingredientData->label)) {
                    $existIngredient = $ingredients->fastSearch($ingredientData->label, 'name');
                }

                if (!$existIngredient) {
                    $existIngredient = new Ingredient();
                    $existIngredient->name = $ingredientData->label ?? $ingredientData->ingredient;
                    $existIngredient->save();

                    $ingredients->push($existIngredient);
                }

                $recipe->ingredients()->syncWithPivotValues([$existIngredient->id], [
                    'volume' => $ingredientData->amount,
                    'volume_type' => VolumeTypeEnum::findFrom($ingredientData->unit),
                    'optional' => $ingredientData->optional ?? false,
                ]);
            }

            DB::commit();
        }
    }

    /**
     * @throws Exception
     */
    private function parseIbaSpecialIngredient(string $special): stdClass
    {
        $specialVariantMap = [
            "3 dashes Strawberry syrup" => [
                'amount' => 3,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Strawberry syrup',
            ],
            "2 dashes Angostura Bitters" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Angostura Bitters',
            ],
            "Few dashes plain water" => [
                'amount' => 3,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Water',
            ],
            "1/2 bar spoon Maraschino" => [
                'amount' => 0.5,
                'unit' => VolumeTypeEnum::BarSpoon->value,
                'ingredient' => 'Maraschino',
            ],
            "1/4 bar spoon Absinthe" => [
                'amount' => 0.25,
                'unit' => VolumeTypeEnum::BarSpoon->value,
                'ingredient' => 'Absinthe',
            ],
            "3 dashes Orange Bitters" => [
                'amount' => 3,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Orange Bitters',
            ],
            "6 Mint sprigs" => [
                'amount' => 6,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Mint sprigs',
            ],
            "2 teaspoons white sugar" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Teaspoon->value,
                'ingredient' => 'White sugar',
            ],
            "Soda water" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Soda water',
            ],
            "Dash of Angostura bitters (optional)" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Angostura bitters',
                'optional' => true,
            ],
            "3 to 4 dashes Angostura bitters" => [
                'amount' => 3,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Angostura bitters',
            ],
            "1 raw egg white (small egg)" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Egg white',
            ],
            "1 dash of Cola" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Cola',
            ],
            "Few drops of Egg White" => [
                'amount' => 3,
                'unit' => VolumeTypeEnum::Drop->value,
                'ingredient' => 'Egg white',
            ],
            "1 dash Angostura Bitters" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Angostura Bitters',
            ],
            "Sugar syrup (according to individual preference of sweetness)" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Sugar syrup',
                'optional' => true,
            ],
            "1 short strong Espresso" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Part->value,
                'ingredient' => 'Espresso',
            ],
            "2 dashes Sugar syrup" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Sugar syrup',
            ],
            "Splash of Soda water" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::Splash->value,
                'ingredient' => 'Soda water',
            ],
            "2 to 3 dashes of Worcestershire Sauce" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Worcestershire Sauce',
            ],
            "Tabasco" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Tabasco',
            ],
            "Celery salt" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Celery salt',
            ],
            "Pepper" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Pepper',
            ],
            "1 dash Lime juice" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Lime juice',
            ],
            "Top with Prosecco" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Prosecco',
            ],
            "2 drops Absinthe" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Drop->value,
                'ingredient' => 'Absinthe',
            ],
            "2 drops Grenadine" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Drop->value,
                'ingredient' => 'Grenadine',
            ],
            "2 drops Peach Bitters" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Drop->value,
                'ingredient' => 'Peach Bitters',
            ],
            "2 Fresh mint leaves" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Fresh mint leaves',
            ],
            "1 teaspoon of brown sugar" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Teaspoon->value,
                'ingredient' => 'Brown sugar',
            ],
            "1 sugar cube" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Cube->value,
                'ingredient' => 'Sugar',
            ],
            "2 dashes Peychaud’s bitters" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Peychaud’s bitters',
            ],
            "A splash of soda water" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Splash->value,
                'ingredient' => 'Soda water',
            ],
            "1 dash Angostura bitters" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Angostura bitters',
            ],
            "1 slice lime in a highball glass" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Slice->value,
                'ingredient' => 'Lime',
            ],
            "4 fresh mint sprigs" => [
                'amount' => 4,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Fresh mint sprigs',
            ],
            "1 teaspoon powdered sugar" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Teaspoon->value,
                'ingredient' => 'Powdered sugar',
            ],
            "2 teaspoons water" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Teaspoon->value,
                'ingredient' => 'Water',
            ],
            "2 bar spoons of Agave nectar" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Slice->value,
                'ingredient' => 'Agave nectar',
            ],
            "1 Egg white" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Egg white',
            ],
            "3 dashes Orange flower water" => [
                'amount' => 3,
                'unit' => VolumeTypeEnum::Dash->value,
                'ingredient' => 'Orange flower water',
            ],
            "2 drops Vanilla extract" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Drop->value,
                'ingredient' => 'Vanilla extract',
            ],
            "half fresh lime cut into 4 wedges" => [
                'amount' => 0.5,
                'unit' => VolumeTypeEnum::Slice->value,
                'ingredient' => 'Lime',
            ],
            "2 teaspoon sugar" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Teaspoon->value,
                'ingredient' => 'Sugar',
            ],
            "1 teaspoon clear honey" => [
                'amount' => 1,
                'unit' => VolumeTypeEnum::Teaspoon->value,
                'ingredient' => 'Honey',
            ],
            "Half slice onion finely chopped" => [
                'amount' => 0.5,
                'unit' => VolumeTypeEnum::Slice->value,
                'ingredient' => 'Onion finely chopped',
            ],
            "Few slices fresh red hot chili peppers" => [
                'amount' => 2,
                'unit' => VolumeTypeEnum::Slice->value,
                'ingredient' => 'Red hot chili peppers',
            ],
            "Few drops Worcestershire sauce" => [
                'amount' => 3,
                'unit' => VolumeTypeEnum::Drop->value,
                'ingredient' => 'Worcestershire sauce',
            ],
            "Salt" => [
                'amount' => 0,
                'unit' => VolumeTypeEnum::UnknownType->value,
                'ingredient' => 'Salt',
            ],
        ];

        if (!isset($specialVariantMap[$special])) {
            throw new Exception();
        }

        $ingredientData = new stdClass();

        $ingredientData->unit = $specialVariantMap[$special]['unit'];
        $ingredientData->amount = $specialVariantMap[$special]['amount'];
        $ingredientData->ingredient = $specialVariantMap[$special]['ingredient'];
        $ingredientData->optional = $specialVariantMap[$special]['optional'] ?? false;

        return $ingredientData;
    }

}
