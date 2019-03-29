<?php
declare(strict_types = 1);

namespace IngeniozIT\Math;

class ActivationFunction
{
    public static function identity(float $val): float
    {
        return $val;
    }

    public static function binaryStep(float $val): float
    {
        return $val < 0.0 ? 0.0 : 1.0;
    }

    /**
     * Get the sigmoid value of a number.
     * @param float $val The value.
     * @return float sigmoid($val).
     */
    public static function sigmoid(float $val): float
    {
        return 1 / (1 + exp(-$val));
    }

    public static function tanh(float $val): float
    {
        return tanh($val);
    }

    public static function relu(float $val): float
    {
        return $val < 0.0 ? 0 : $val;
    }

    public static function leakyRelu(float $val): float
    {
        return $val < 0.0 ? 0.01 * $val : $val;
    }

    public static function gaussian(float $val): float
    {
        return exp(-1 * ($val ** 2));
    }
}
