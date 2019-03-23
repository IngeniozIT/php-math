<?php
declare(strict_types = 1);

namespace IngeniozIT\Math;

class ActivationFunction
{
    /**
     * Get the sigmoid value of a number.
     * @param float $val The value.
     * @return float sigmoid($val).
     */
    public static function sigmoid(float $val): float
    {
        return 1 / (1 + exp(-$val));
    }
}
