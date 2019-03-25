<?php
declare(strict_types = 1);

namespace IngeniozIT\Math;

class Vector
{
    /**
     * Get the distance between two points represented by vectors.
     * @param array $pointA The first point.
     * @param array $pointB The second point.
     * @return float
     */
    public static function distance(array $pointA, array $pointB): float
    {
        return array_sum(
            array_map(
                'self::subSquare',
                $pointA,
                $pointB
            )
        ) ** 0.5;
    }

    /**
     * Get the length of a vector.
     * @param array $point The vector.
     * @return float
     */
    public static function length(array $vector): float
    {
        return (array_sum(array_map('self::square', $vector))) ** 0.5;
    }

    /**
     * Get the sum of multiple vectors.
     * @param array $points A list of vectors.
     * @return array
     */
    public static function sum(array $points): array
    {
        $sum = array_shift($points);
        foreach ($points as $point) {
            foreach ($point as $i => $val) {
                $sum[$i] += $val;
            }
        }
        return $sum;
    }

    /**
     * Divide a vector by a scalar value.
     * @param array $vector The vector.
     * @param float $nb The scalar value.
     * @return array
     */
    public static function scalarDiv(array $vector, float $nb): array
    {
        foreach ($vector as &$v) {
            $v /= $nb;
        }
        return $vector;
    }

    /**
     * Get the average of multiple points.
     * @param array $points A list of vectors representing the points.
     * @return array
     */
    public static function mean(array $points): array
    {
        return self::scalarDiv(self::sum($points), count($points));
    }

    // Inner methods.

    protected static function square(float $val): float
    {
        return $val ** 2;
    }

    protected static function subSquare(float $a, float $b): float
    {
        return ($a - $b) ** 2;
    }
}
