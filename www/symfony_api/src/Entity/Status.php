<?php

namespace App\Entity;

enum Status: string
{
    case done = "done";
    case todo = "todo";

    /**
     * random value for fixture
     * @return string
     */
    public static function randomValue(): string
    {
        $arr = array_column(self::cases(), 'value');

        return $arr[array_rand($arr)];
    }
}
