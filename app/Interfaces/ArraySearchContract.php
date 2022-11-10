<?php

namespace App\Interfaces;

interface ArraySearchContract
{
    /**
     * @param  string|int|float  $needle
     * @param  array<string|int|float>  $haystack
     * @param  string  $key
     * @return mixed
     */
    public function __invoke(string|int|float $needle, array $haystack, string $key = ''): mixed;

}
