<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface CocktailParser
{
    public function uploadIngredients(Collection $existsIngredients): void;

    public function uploadCocktails(Collection $ingredients, Collection $existsRecipes): void;
}
