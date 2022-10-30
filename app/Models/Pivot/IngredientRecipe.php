<?php

namespace App\Models\Pivot;

use App\Enums\VolumeTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperIngredientRecipe
 */
class IngredientRecipe extends Pivot
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'ingredient_id',
        'recipe_id',
        'volume',
        'volume_type',
    ];
    protected $casts = [
        'volume_type' => VolumeTypeEnum::class,
        'volume' => 'float',
    ];
}
