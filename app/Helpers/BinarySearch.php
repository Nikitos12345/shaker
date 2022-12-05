<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Interfaces\ArraySearchContract;
use App\Interfaces\ArraySortContract;
use App\Traits\ValidateUsableValue;
use Illuminate\Support\ItemNotFoundException;
use InvalidArgumentException;

final class BinarySearch implements ArraySearchContract
{
    use ValidateUsableValue {
        getValidValue as private;
    }

    /** @var array<string|int|float|object|array<string|int|float>> $haystack */
    public array $haystack = [];
    public string|int|float $needle = '';
    public string $key = '';
    private ArraySortContract $sorter;

    public function __construct()
    {
        $this->sorter = app(ArraySortContract::class);
    }

    /**
     * @param  string|int|float  $needle
     * @param  array<string|int|float|object|array<string|int|float>>  $haystack
     * @param  string  $key
     * @phpstan-ignore-next-line
     * @return float|string|int|false|object|null|array
     */
    public function __invoke(
        string|int|float $needle,
        array $haystack,
        string $key = ''
    ): array|float|string|int|false|null|object {
        if (empty($haystack)) {
            return false;
        }

        $this->haystack = $this->sorter->sort($haystack, $key);
        $this->needle = $needle;
        $this->key = $key;

        try {
            return $this->search();
        } catch (ItemNotFoundException|InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @return string|int|float|array<string|int|float|false|object>|false|object
     */
    private function search(): string|int|float|array|false|object
    {
        $high = count($this->haystack) - 1;
        $low = 0;

        while ($low <= $high) {
            $halfArrayLength = intval(($low + $high) / 2);

            $middleValue = $this->getMiddleValue($halfArrayLength);

            if ($this->needle === $middleValue) {
                return $this->haystack[$halfArrayLength];
            }

            if ($this->needle > $middleValue) {
                $low = $halfArrayLength + 1;
                continue;
            }

            $high = $halfArrayLength - 1;
        }

        return false;
    }

    /**
     * @param  int  $middleKey
     * @return string|int|float|array<string|int|float>|object
     */
    private function getMiddleValue(int $middleKey): string|int|float|array|object
    {
        $middleValue = $this->haystack[$middleKey];

        return $this->getValidValue($middleValue);
    }
}
