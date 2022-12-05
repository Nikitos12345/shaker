<?php

namespace App\Traits;

use Illuminate\Support\ItemNotFoundException;
use InvalidArgumentException;

trait ValidateUsableValue
{
    public function getValidValue(string|int|float|array|object $value): string|int|float|array|object
    {
        if (empty($this->key)) {
            return $value;
        }

        if (is_numeric($value)) {
            throw new InvalidArgumentException();
        }

        if (is_string($value)) {
            throw new InvalidArgumentException();
        }

        if (is_array($value) && !isset($value[$this->key])) {
            throw new ItemNotFoundException();
        }

        if (is_object($value) && !isset($value->{$this->key})) {
            throw new ItemNotFoundException();
        }

        $value = is_array($value) ? $value[$this->key] : $value->{$this->key};

        if (is_object($value)) {
            throw new InvalidArgumentException();
        }

        if (is_array($value)) {
            throw new InvalidArgumentException();
        }

        if (is_bool($value)) {
            throw new InvalidArgumentException();
        }

        return $value;
    }
}
