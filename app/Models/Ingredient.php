<?php

namespace App\Models;

use App\Models\Pivot\IngredientRecipe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @phpstan-ignore-next-line
 * @mixin IdeHelperIngredient
 */
class Ingredient extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name', 'description', 'taste'];

    protected $fillable = [
        'name',
        'description',
        'taste',

    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)->using(IngredientRecipe::class);
    }
}
