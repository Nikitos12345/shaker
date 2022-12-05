<?php

namespace App\Providers;

use App\Helpers\BinarySearch;
use App\Helpers\QuickSort;
use App\Interfaces\ArraySearchContract;
use App\Interfaces\ArraySortContract;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** @var string[] */
    public array $bindings = [
        ArraySearchContract::class => BinarySearch::class,
        ArraySortContract::class => QuickSort::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Collection::macro('fastSearch', function (string|int|float $search, string $key = '') {
            $searcher = app(ArraySearchContract::class);
            /** @var Collection $collection */
            $collection = $this;
            /** @var array<string|int|float> $array */
            $array = $collection->all();

            return $searcher($search, $array, $key);
        });

        Collection::macro('betterSearch', function (string $key = '', string $direction = 'abc') {
            $sorter = app(ArraySortContract::class);

            /** @var Collection $collection */
            $collection = $this;
            /** @var array<string|int|float> $array */
            $array = $collection->all();

            if ($direction === 'desc') {
                $array = $sorter->rsort($array, $key);
            } else {
                $array = $sorter->sort($array, $key);
            }

            return collect($array);
        });
    }
}
