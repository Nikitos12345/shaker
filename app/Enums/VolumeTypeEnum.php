<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum VolumeTypeEnum: string
{
    case Ml = 'ml';
    case Drop = 'drop';
    case Part = 'part';
    case Cl = 'cl';
    case Dash = 'dash';
    case Splash = 'splash';
    case Whole = 'whole';
    case BarSpoon = 'bar spoon';
    case Teaspoon = 'teaspoon';
    case Item = 'item';
    case Shot = 'shot';
    case Cube = 'cube';
    case Slice = 'slice';
    case UnknownType = '';

    public static function findFrom(string $value): self
    {
        $result = self::tryFrom($value);

        if ($result) {
            return $result;
        }

        $singular = Str::singular($value);
        $result = self::tryFrom($singular);

        if ($result) {
            return $result;
        }

        return self::UnknownType;
    }
}
