<?php
declare(strict_types = 1);

namespace IngeniozIT\Math\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\Math\KMeans;
use IngeniozIT\Math\Vector;
use IngeniozIT\Math\Random;

class KMeansTest extends TestCase
{
    public function testValues()
    {
        $values1 = [
            0 => [0, 1, 2],
            1 => [3, 4, 5],
            2 => [6, 7, 8],
        ];

        $kMeans = new KMeans($values1);

        $this->assertEquals($values1, $kMeans->values());

        $values2 = [
            42 => [0, 1, 2],
            50 => [0.5, 1.5, 2.5],
            2 => [6, 7, 8],
            200 => [6.5, 7.5, 8.5],
        ];

        $kMeans = new KMeans($values2);

        $this->assertEquals($values2, $kMeans->values());

        return $kMeans;
    }

    /**
     * @depends testValues
     */
    public function testAlgoNotRun(KMeans $kMeans)
    {
        $this->assertNull($kMeans->nbClusters());
        $this->assertNull($kMeans->avgDistanceToCentroids());
        $this->assertNull($kMeans->clusters());
        $this->assertNull($kMeans->centroids());
    }

    /**
     * @depends testValues
     */
    public function testInvalid(KMeans $kMeans)
    {
        $this->expectException(\InvalidArgumentException::class);
        $kMeans->classify(0);
    }

    /**
     * @depends testValues
     */
    public function testClassify(KMeans $kMeans)
    {
        $kMeans->classify(1);
        $this->assertEquals(1, $kMeans->nbClusters());
        $this->assertEquals([Vector::mean($kMeans->values())], $kMeans->centroids());
        $this->assertEquals([[2, 42, 50, 200]], $kMeans->clusters());
        $this->assertEquals(51962, round($kMeans->avgDistanceToCentroids() * 10000));

        $kMeans->classify(2);
        $this->assertEquals(2, $kMeans->nbClusters());
        $centroids = $kMeans->centroids();
        usort(
            $centroids, function (array $a, array $b) {
                return array_sum($a) <=> array_sum($b);
            }
        );
        $this->assertEquals([[0.25, 1.25, 2.25], [6.25, 7.25, 8.25]], $centroids);
        $this->assertEquals(4330, round($kMeans->avgDistanceToCentroids() * 10000));

        $kMeans->classify(4);
        $this->assertEquals(4, $kMeans->nbClusters());
        $this->assertEquals(0, $kMeans->avgDistanceToCentroids());
    }

    /**
     * @depends testValues
     */
    public function testClassifyAndOptimize(KMeans $kMeans)
    {
        $kMeans->classifyAndOptimize();
        $this->assertEquals(2, $kMeans->nbClusters());
    }

    public function testClassifyAndOptimizeOneCluster()
    {
        $values = [
            0 => [0, 1, 2],
            1 => [0, 1, 2],
            2 => [0, 1, 2],
        ];

        $kMeans = new KMeans($values);

        $kMeans->classifyAndOptimize();
        $this->assertEquals(1, $kMeans->nbClusters());
    }

    public function testClassifyAndOptimizeFourClusters()
    {
        $values = [];

        for ($i = 0; $i < 100; ++$i) {
            $values[] = [
                ($i % 4) * 100 + Random::frand(-1, 1),
                ($i % 4) * 100 + Random::frand(-1, 1),
                ($i % 4) * 100 + Random::frand(-1, 1),
            ];
        }

        $kMeans = new KMeans($values);

        $kMeans->classifyAndOptimize();
        $this->assertEquals(4, $kMeans->nbClusters());
    }
}
