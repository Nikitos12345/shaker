<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Interfaces\ArraySearchContract;
use Illuminate\Support\Arr;
use Illuminate\Support\ItemNotFoundException;
use InvalidArgumentException;

final class BinarySearch implements ArraySearchContract
{
    /** @var array<string|int|float|object|array<string|int|float>> $haystack */
    public array $haystack = [];
    public string|int|float $needle = '';
    public string $key = '';

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

        $this->haystack = $this->sort($haystack, $key);
        $this->needle = $needle;
        $this->key = $key;

        try {
            return $this->search();
        } catch (ItemNotFoundException|InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param  array<string|int|float|array<string|int|float>|object>  $sortableArray
     * @param  string  $key
     * @return array<string|int|float|array<string|int|float>|object>
     * @todo Переписать на нормальную сортировку
     *
     */
    private function sort(array $sortableArray, string $key): array
    {
        if (empty($key)) {
            return array_values(Arr::sort($sortableArray));
        }


        return array_values(
            Arr::sort(
                $sortableArray,
                fn(mixed $value) => is_array($value) ? $value[$key] : (is_object($value) ? $value->$key : $value)
            )
        );
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

            $middleValues = $this->getMiddleValue($halfArrayLength);

            if ($this->needle === $middleValues) {
                return $this->haystack[$halfArrayLength];
            }

            if ($this->needle > $middleValues) {
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

        if (empty($this->key)) {
            return $middleValue;
        }

        if (is_numeric($middleValue)) {
            throw new InvalidArgumentException();
        }

        if (is_string($middleValue)) {
            throw new InvalidArgumentException();
        }

        if (is_array($middleValue) && !isset($middleValue[$this->key])) {
            throw new ItemNotFoundException();
        }

        if (is_object($middleValue) && !isset($middleValue->{$this->key})) {
            throw new ItemNotFoundException();
        }

        $middleValue = is_array($middleValue) ? $middleValue[$this->key] : $middleValue->{$this->key};

        if (is_object($middleValue)) {
            throw new InvalidArgumentException();
        }

        if (is_array($middleValue)) {
            throw new InvalidArgumentException();
        }

        if (is_bool($middleValue)) {
            throw new InvalidArgumentException();
        }

        return $middleValue;
    }
}
