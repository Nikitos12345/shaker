<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Pagination\LengthAwarePaginator;

class RecipeController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        $recipes = Recipe::with('ingredients')->paginate();


        return $recipes;
    }
}
