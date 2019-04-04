<?php
declare(strict_types = 1);

namespace IngeniozIT\Math\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\Math\ActivationFunction;

class ActivationFunctionTest extends TestCase
{
    public function testIdentity()
    {
        for ($i = -2.0; $i <= 2.0; $i += 0.2) {
            $this->assertEquals($i, ActivationFunction::identity($i));
        }
    }

    public function testBinaryStep()
    {
        for ($i = -2.0; $i <= 2.0; $i += 0.2) {
            $this->assertEquals($i >= 0 ? 1 : 0, ActivationFunction::binaryStep($i));
        }
    }

    public function testSigmoid()
    {
        $sigmoidValues = [
            [-5, 0.00669],
            [-4, 0.01798],
            [-3, 0.04742],
            [-2, 0.11920],
            [-1, 0.26894],
            [0, 0.5],
            [1, 0.73105],
            [2, 0.88079],
            [3, 0.95257],
            [4, 0.98201],
            [5, 0.99330],
        ];

        foreach ($sigmoidValues as $sigmoidValue) {
            $this->assertLessThanOrEqual(
                0.00001,
                abs(
                    ActivationFunction::sigmoid($sigmoidValue[0]) -
                    $sigmoidValue[1]
                )
            );
        }
    }

    public function testTanh()
    {
        for ($i = -2.0; $i <= 2.0; $i += 0.2) {
            $this->assertEquals(tanh($i), ActivationFunction::tanh($i));
        }
    }

    public function testReLU()
    {
        for ($i = -2.0; $i <= 2.0; $i += 0.2) {
            $this->assertEquals($i >= 0 ? $i : 0, ActivationFunction::relu($i));
        }
    }

    public function testLeakyReLU()
    {
        for ($i = -2.0; $i <= 2.0; $i += 0.2) {
            $this->assertEquals($i >= 0 ? $i : 0.01 * $i, ActivationFunction::leakyRelu($i));
        }
    }

    public function testGaussian()
    {
        $values = [
            '-2' => 0.018315638888734,
            '-1.8' => 0.039163895098987,
            '-1.6' => 0.0773047404433,
            '-1.4' => 0.14085842092104,
            '-1.2' => 0.23692775868212,
            '-1' => 0.36787944117144,
            '-0.8' => 0.52729242404305,
            '-0.6' => 0.69767632607103,
            '-0.4' => 0.85214378896621,
            '-0.2' => 0.96078943915232,
            '0' => 1,
            '0.2' => 0.96078943915232,
            '0.4' => 0.85214378896621,
            '0.6' => 0.69767632607103,
            '0.8' => 0.52729242404305,
            '1' => 0.36787944117144,
            '1.2' => 0.23692775868212,
            '1.4' => 0.14085842092105,
            '1.6' => 0.0773047404433,
            '1.8' => 0.039163895098987,
            '2' => 0.018315638888734,
        ];

        foreach ($values as $value => $result) {
            $this->assertEquals($result, ActivationFunction::gaussian((float)$value));
        }
    }
}
