<?php

namespace App\Entity;

enum Description: string
{
    case one = "honda";
    case two = "bmw";
    case three = "ford";
    case four = "ferrari";
    case five = "toyota";
    case six = "fiat";

    /**
     * @return string
     */
    public static function randomValue(): string
    {
        $description = Description::cases(); 
        $randomDivision = $description[rand(0, count($description)-1)]; 
        $string = $randomDivision->name . " " . $randomDivision->value;
        
        return $string;
    }
}