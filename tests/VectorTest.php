<?php
declare(strict_types = 1);

namespace IngeniozIT\Math\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\Math\Vector;

class VectorTest extends TestCase
{
    public function testDistance()
    {
        $distances = [
            [0.0, [0, 0], [0, 0]],
            [1.0, [0, 0], [0, 1]],
            [5.0, [0, 0], [3, 4]],
            [10.0, [-3, -4], [3, 4]],
        ];

        foreach ($distances as $distance) {
            $this->assertEquals($distance[0], Vector::distance($distance[1], $distance[2]));
        }
    }

    public function testLength()
    {
        $lengths = [
            [0.0, [0, 0]],
            [1.0, [0, 1]],
            [5.0, [3, 4]],
        ];

        foreach ($lengths as $length) {
            $this->assertEquals($length[0], Vector::length($length[1]));
        }
    }

    public function testSum()
    {
        $sums = [
            [[0, 0], [[0, 0], [0, 0]]],
            [[0, 0], [[0, 0], [-1, -1], [1, 1]]],
            [[15, 18], [[3, 4], [5, 6], [7, 8]]],
        ];

        foreach ($sums as $sum) {
            $this->assertEquals($sum[0], Vector::sum($sum[1]));
        }
    }

    public function testScalarDiv()
    {
        $divs = [
            [
                [0, 0], 5, [0, 0],
                [10, 5], 5, [2, 1],
                [10, 5, 20], 10, [1, 0.5, 2],
            ]
        ];

        foreach ($divs as $div) {
            $this->assertEquals($div[2], Vector::scalarDiv($div[0], $div[1]));
        }
    }

    public function testMean()
    {
        $means = [
            [[0, 0], [[0, 0]]],
            [[0, 0], [[-1, -1], [1, 1]]],
        ];

        foreach ($means as $mean) {
            $this->assertEquals($mean[0], Vector::mean($mean[1]));
        }
    }
}
