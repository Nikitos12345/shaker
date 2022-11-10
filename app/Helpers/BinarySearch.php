<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Interfaces\ArraySearchContract;
use Illuminate\Support\Arr;
use Illuminate\Support\ItemNotFoundException;
use InvalidArgumentException;

final class BinarySearch implements ArraySearchContract
{
    /**
     * @param  string|int|float  $needle
     * @param  array<string|int|float>  $haystack
     * @param  string  $key
     * @return string|int|float|array<string|int|float>|false|null
     */
    public function __invoke(
        string|int|float $needle,
        array $haystack,
        string $key = ''
    ): string|int|float|array|false|null {
        if (empty($haystack)) {
            return false;
        }

        $haystack = $this->sort($haystack, $key);

        try {
            $result = $this->search($needle, $haystack, $key);
        } catch (ItemNotFoundException|InvalidArgumentException) {
            return null;
        }

        return $result;
    }

    /**
     * @param  array<string|int|float|array<string|int|float>>  $sortableArray
     * @param  string  $key
     * @return array<string|int|float|array<string|int|float>>
     */
    private function sort(array $sortableArray, string $key): array
    {
        if (empty($key)) {
            return Arr::sort($sortableArray);
        }

        return Arr::sort(
            $sortableArray,
            fn(mixed $value) => is_array($value) ? $value[$key] : $value
        );
    }

    /**
     * @param  string|int|float  $needle
     * @param  array<string|int|float|array<string|int|float>>  $haystack
     * @param  string  $key
     * @return string|int|float|array<string|int|float>|false
     * @throws ItemNotFoundException
     */
    private function search(string|int|float $needle, array $haystack, string $key): string|int|float|array|false
    {
        $arrayLength = count($haystack);

        if ($arrayLength === 1) {
            $result = $this->getMiddleValues($haystack, $key, 0);

            return $result === $needle ? $result : false;
        }

        $halfArrayLength = intval(ceil($arrayLength / 2));

        $middleValues = $this->getMiddleValues($haystack, $key, $halfArrayLength);

        if ($needle === $middleValues) {
            return $haystack[$halfArrayLength];
        }

        if ($needle > $middleValues) {
            $needleChunk = array_splice($haystack, $halfArrayLength, $arrayLength);
            return $this->search($needle, $needleChunk, $key);
        }

        $needleChunk = array_splice($haystack, 0, $halfArrayLength);

        return $this->search($needle, $needleChunk, $key);
    }

    /**
     * @param  array<string|int|float|array<string|int|float>>  $haystack
     * @param  string  $key
     * @param  int  $halfArrayLength
     * @return string|int|float|array<string|int|float>
     */
    private function getMiddleValues(array $haystack, string $key, int $halfArrayLength): string|int|float|array
    {
        $middleValue = $haystack[$halfArrayLength];

        if (empty($key)) {
            return $middleValue;
        }

        if (!is_array($middleValue)) {
            throw new ItemNotFoundException();
        }

        if (!array_key_exists($key, $middleValue)) {
            throw new ItemNotFoundException();
        }

        $middleValue = $middleValue[$key];

        /** @phpstan-ignore-next-line */
        if (is_object($middleValue)) {
            throw new InvalidArgumentException();
        }

        /** @phpstan-ignore-next-line */
        if (is_array($middleValue)) {
            throw new InvalidArgumentException();
        }

        /** @phpstan-ignore-next-line */
        if (is_bool($middleValue)) {
            throw new InvalidArgumentException();
        }

        return $middleValue;
    }
}
