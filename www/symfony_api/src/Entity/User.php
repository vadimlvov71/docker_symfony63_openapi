<?php

namespace App\Entity;

enum User: string
{
    case User1 = "1";
    case User2 = "2";

    /**
     * randomly selects a user for fixture
     * @return string
     */
    public static function randomValue(): string
    {
        $arr = array_column(self::cases(), 'value');

        return $arr[array_rand($arr)];
    }
}