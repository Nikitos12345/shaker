<?php

namespace App\Models;

use App\Models\Pivot\IngredientRecipe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @phpstan-ignore-next-line
 * @mixin IdeHelperRecipe
 */
class Recipe extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name', 'description', 'details'];

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)->using(IngredientRecipe::class);
    }
}
