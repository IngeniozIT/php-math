<?php
declare(strict_types = 1);

namespace IngeniozIT\Math;

class Random
{
    protected static $randMax = null;

    /**
     * Get a random float number between two values.
     * @param float $min The minimum value.
     * @param float $max The maximum value.
     * @return float The random number.
     */
    public static function frand(float $min = 0.0, float $max = 1.0): float
    {
        if (null === self::$randMax) {
            self::$randMax = getrandmax();
        }
        return ($min === 0.0 && $max === 1.0) ?
            rand() / self::$randMax :
            rand() / self::$randMax * abs($max - $min) + $min;
    }

    /**
     * Get a random float number between two values with a normal distribution.
     * @param float $mean The mean value.
     * @param float $stdDeviation The standard deviation.
     * @return float The random number.
     */
    public static function nrand(float $mean, float $stdDeviation): float
    {
        return (
            ((-2 * log(self::frand())) ** 0.5) *
            cos(2 * M_PI * self::frand()) *
            $stdDeviation
        ) + $mean;
    }
}
