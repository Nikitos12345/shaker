<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App;
use App\Enums\AvailableTranslationEnum;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Console\Command;
use Throwable;

class ParseCocktailsCommand extends Command
{
    protected $signature = 'parse:cocktails';

    protected $description = 'Parse cocktails';

    private array $parsers = [
        App\Services\Parsers\SvetalanaCocktailsParser::class,
        App\Services\Parsers\IbaCocktailsParser::class,
    ];

    public function handle()
    {
        App::setLocale(AvailableTranslationEnum::English->value);

        $ingredients = Ingredient::all();
        $existsRecipes = Recipe::all();
        $instances = $this->createInstances();

        foreach ($instances as $instance) {
            try {
                $instance->uploadIngredients($ingredients);
                $instance->uploadCocktails($ingredients, $existsRecipes);
            } catch (Throwable $exception) {
                logger()
                    ->channel('cocktails-parser')
                    ->error($exception);
            }
        }
    }

    /**
     * @return array<App\Interfaces\CocktailParser>
     */
    private function createInstances(): array
    {
        $instances = [];

        foreach ($this->parsers as $parser) {
            $instances[] = new $parser();
        }

        return $instances;
    }
}
