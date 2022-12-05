<?php

namespace App\Interfaces;

interface ArraySortContract
{
    public function sort(array $array, string $key = ''): array;

    public function rsort(array $array, string $key = ''): array;
}
