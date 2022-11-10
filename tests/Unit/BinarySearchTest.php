<?php

namespace Tests\Unit;

use App\Helpers\BinarySearch;
use PHPUnit\Framework\TestCase;
use stdClass;

class BinarySearchTest extends TestCase
{
    /**
     * @dataProvider simpleValuesDataProvider
     * @param  string|int|float  $needle
     * @param  array  $array
     * @return void
     */
    public function test_search_simple_value(string|int|float $needle, array $array): void
    {
        $binarySearch = new BinarySearch();

        $result = $binarySearch($needle, $array);

        $this->assertSame($needle, $result);
    }

    public function simpleValuesDataProvider(): array
    {
        $arrayOfNumbers = range(1, 100);
        shuffle($arrayOfNumbers);

        $arrayOfStrings = range('a', 'z');
        shuffle($arrayOfStrings);

        return [
            [
                1,
                [1, 2, 3],
            ],
            [
                'test',
                ['a', 'test', 'b'],
            ],
            [
                1.2,
                [1, 2.3, 1.2, 3.3, 4]
            ],
            [
                98,
                $arrayOfNumbers,
            ],
            [
                'y',
                $arrayOfStrings
            ],
            [
                0,
                [0, 1, 2]
            ],
            [
                '0',
                ['0', '1', '2']
            ],
            [
                '',
                ['']
            ]
        ];
    }

    /**
     * @dataProvider arrayOfAssocArrayDataProvider
     *
     * @param  string|int|float  $needle
     * @param  array  $array
     * @param  string  $key
     * @return void
     */
    public function test_search_on_array_of_assoc_array(string|int|float $needle, array $array, string $key): void
    {
        $binarySearch = new BinarySearch();

        $result = $binarySearch($needle, $array, $key);

        $this->assertEquals($needle, $result[$key]);
    }

    public function arrayOfAssocArrayDataProvider(): array
    {
        return [
            [
                6,
                [
                    ['key' => 1],
                    ['key' => 2],
                    ['key' => 3],
                    ['key' => 4],
                    ['key' => 6],
                ],
                'key'
            ],
            [
                'Absent',
                [
                    ['name' => 'Aperol', 'taste' => 'bitter'],
                    ['name' => 'Rom', 'taste' => 'bitter'],
                    ['name' => 'Kahula', 'taste' => 'sweet'],
                    ['name' => 'Absent', 'taste' => 'bitter'],
                    ['name' => 'Beer', 'taste' => 'bitter'],
                ],
                'name'
            ]
        ];
    }

    /**
     * @dataProvider simpleValuesDataProvider
     *
     * @param  string|int|float  $needle
     * @param  array  $array
     * @return void
     */
    public function test_return_null_if_key_dont_exist(string|int|float $needle, array $array): void
    {
        $binarySearch = new BinarySearch();

        $this->assertEquals(null, $binarySearch($needle, $array, 'test'));
    }

    /**
     * @dataProvider dontExistsValueDataProvider
     *
     * @param  string|int|float  $needle
     * @param  array  $array
     * @return void
     */
    public function test_return_false_if_value_doesnt_find(string|int|float $needle, array $array): void
    {
        $binarySearch = new BinarySearch();

        $this->assertEquals(false, $binarySearch($needle, $array));
    }

    public function dontExistsValueDataProvider(): array
    {
        return [
            [
                1,
                [2, 3, 4]
            ],
            [
                'a',
                ['b', 'c', 'd']
            ]
        ];
    }

    /**
     * @dataProvider doublesArraysDataProvider
     *
     * @param  string|int|float  $needle
     * @param  array  $array
     * @return void
     */
    public function test_return_one_value_if_array_has_doubles(string|int|float $needle, array $array): void
    {
        $binarySearch = new BinarySearch();

        $this->assertSame($needle, $binarySearch($needle, $array));
    }

    public function doublesArraysDataProvider(): array
    {
        return [
            [
                1,
                [1, 1, 1, 1, 1, 1]
            ],
            [
                'a',
                ['a', 'a', 'a', 'a', 'a']
            ]
        ];
    }

    /**
     * @dataProvider wrongArraysDataProvider
     *
     * @param  string|int|float  $needle
     * @param  array  $array
     * @param  string  $key
     * @return void
     */
    public function test_got_null_if_key_value_not_valid(string|int|float $needle, array $array, string $key): void
    {
        $binarySearch = new BinarySearch();

        $this->assertSame(null, $binarySearch($needle, $array, $key));
    }

    public function wrongArraysDataProvider(): array
    {
        return [
            [
                1,
                [
                    ['numb' => [1]],
                    ['numb' => [2]],
                    ['numb' => [3]],
                ],
                'numb'
            ],
            [
                'test',
                [
                    ['key' => new stdClass()]
                ],
                'key'
            ],
            [
                '1',
                [
                    ['key' => true]
                ],
                'key'
            ]
        ];
    }

    /**
     * @dataProvider arrayOfObjectsDataProvider
     *
     * @param  string|int|float  $needle
     * @param  array  $array
     * @param  string  $key
     * @return void
     */
    public function test_return_object_if_is_array_of_object(string|int|float $needle, array $array, string $key): void
    {
        $binarySearch = new BinarySearch();

        $expectedResult = new stdClass();
        $expectedResult->$key = $needle;

        $result = $binarySearch($needle, $array, $key);

        $this->assertNotEquals(false, $result);

        $this->assertSame($expectedResult->$key, $result->$key);
    }

    public function arrayOfObjectsDataProvider(): array
    {
        $ex1 = new stdClass();
        $ex1->name = 'test';
        $ex2 = new stdClass();
        $ex2->name = '1';
        $ex3 = new stdClass();
        $ex3->name = '2';

        return [
            [
                'test',
                [
                    $ex1,
                    $ex2,
                    $ex3,
                ],
                'name'
            ]
        ];
    }
}
