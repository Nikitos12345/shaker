<?php

namespace App\Helpers;

use App\Interfaces\ArraySortContract;
use App\Traits\ValidateUsableValue;

final class QuickSort implements ArraySortContract
{
    use ValidateUsableValue {
        getValidValue as private;
    }


    private string $key = '';
    private string $direction = 'abc';

    public function sort(array $array, string $key = ''): array
    {
        return $this->sorting($array, $key);
    }

    private function sorting(array $array, string $key, string $direction = 'abc'): array
    {
        $this->key = $key;
        $this->direction = $direction;

        return $this->quickSort($array);
    }

    private function quickSort(array $array): array
    {
        if (count($array) < 2) {
            return $array;
        }

        $middleKey = intdiv(count($array), 2);
        $middleValue = $this->getValue($array, $middleKey);

        $lessSubarray = [];
        $highSubarray = [];

        for ($i = 0; $i < count($array); $i++) {
            $checkedValue = $this->getValue($array, $i);

            if ($i === $middleKey) {
                continue;
            }

            if ($checkedValue < $middleValue) {
                $lessSubarray[] = $array[$i];
            } else {
                $highSubarray[] = $array[$i];
            }
        }

        $lessSubarray = $this->quickSort($lessSubarray);
        $highSubarray = $this->quickSort($highSubarray);

        if ($this->direction === 'abc') {
            return array_merge($lessSubarray, [$array[$middleKey]], $highSubarray);
        } else {
            return array_merge($highSubarray, [$array[$middleKey]], $lessSubarray);
        }
    }

    private function getValue(array $array, int $index): string|float|int
    {
        $value = $array[$index];

        return $this->getValidValue($value);
    }

    public function rsort(array $array, string $key = ''): array
    {
        return $this->sorting($array, $key, 'desc');
    }
}
