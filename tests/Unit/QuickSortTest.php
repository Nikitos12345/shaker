<?php

namespace Tests\Unit;

use App\Helpers\QuickSort;
use PHPUnit\Framework\TestCase;
use stdClass;

class QuickSortTest extends TestCase
{
    /**
     * @dataProvider unsortedArraysDataProvider
     * @param  array  $array
     * @return void
     */
    public function test_check_abc_sort(array $array): void
    {
        $qsort = new QuickSort();

        $result = $qsort->sort($array);

        sort($array);
        $same = true;

        for ($i = 0; $i < count($array); $i++) {
            if ($result[$i] !== $array[$i]) {
                $same = false;
            }
        }

        $this->assertTrue($same);
    }

    public function unsortedArraysDataProvider(): array
    {
        $randomArray = range(0, 100);
        shuffle($randomArray);

        return [
            [[5, 4, 1]],
            [[3, 1, 2]],
            [[1, 1, 1, 1]],
            [array_values($randomArray)],
        ];
    }

    /**
     * @dataProvider unsortedArraysDataProvider
     * @param  array  $array
     * @return void
     */
    public function test_check_reverse_sort(array $array): void
    {
        $qsort = new QuickSort();

        $result = $qsort->rsort($array);

        rsort($array);
        $same = true;

        for ($i = 0; $i < count($array); $i++) {
            if ($result[$i] !== $array[$i]) {
                $same = false;
            }
        }

        $this->assertTrue($same);
    }

    /**
     * @dataProvider arrayOfObjectDataProvider
     * @param  array  $array
     * @param  string  $key
     * @return void
     */
    public function test_sort_by_key(array $array, string $key): void
    {
        $qsort = new QuickSort();

        $result = $qsort->rsort($array, $key);

        $this->assertSame(array_pop($array)->$key, $result[0]->$key);
    }

    public function arrayOfObjectDataProvider(): array
    {
        $array = [];

        for ($i = 0; $i < 10; $i++) {
            $obj = new stdClass();
            $obj->name = "test$i";

            $array[] = $obj;
        }

        return [
            [
                $array,
                'name'
            ]
        ];
    }
}
