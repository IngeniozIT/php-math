<?php
declare(strict_types = 1);

namespace IngeniozIT\Math;

class ActivationFunction
{
    /**
     * Returns the number given.
     *
     * @param  float $val The value.
     * @return float $val.
     */
    public static function identity(float $val): float
    {
        return $val;
    }

    /**
     * Get the binary step value of a number.
     *
     * @param  float $val The value.
     * @return float 1 if $val is >= 0, 0 otherwise.
     */
    public static function binaryStep(float $val): float
    {
        return $val > 0.0 ? 1.0 : 0.0;
    }

    /**
     * Get the sigmoid value of a number.
     *
     * @param  float $val The value.
     * @return float sigmoid($val).
     */
    public static function sigmoid(float $val): float
    {
        return 1 / (1 + exp(-$val));
    }

    /**
     * Get the tanh value of a number.
     *
     * @param  float $val The value.
     * @return float tanh($val).
     */
    public static function tanh(float $val): float
    {
        return tanh($val);
    }

    /**
     * Get the ReLU value of a number.
     *
     * @param  float $val The value.
     * @return float ReLU($val).
     */
    public static function relu(float $val): float
    {
        return $val < 0.0 ? 0 : $val;
    }

    /**
     * Get the leaky ReLU value of a number.
     *
     * @param  float $val The value.
     * @return float LeakyReLU($val).
     */
    public static function leakyRelu(float $val): float
    {
        return $val < 0.0 ? 0.01 * $val : $val;
    }

    /**
     * Get the gaussian value of a number.
     *
     * @param  float $val The value.
     * @return float gaussian($val).
     */
    public static function gaussian(float $val): float
    {
        return exp(-1 * ($val * $val));
    }
}
