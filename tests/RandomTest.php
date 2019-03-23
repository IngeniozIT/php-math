<?php
declare(strict_types = 1);

namespace IngeniozIT\Math\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\Math\Random;

class RandomTest extends TestCase
{
    public function testFrand()
    {
        $min = null;
        $max = null;
        for ($i = 1000; $i; --$i) {
            $frand = Random::frand();
            $min = min($min ?? 50, $frand);
            $max = max($max ?? -50, $frand);
        }

        $this->assertLessThanOrEqual(1, $max);
        $this->assertGreaterThanOrEqual(0, $min);

        $min = null;
        $max = null;
        for ($i = 1000; $i; --$i) {
            $frand = Random::frand(-42, 42);
            $min = min($min ?? 50, $frand);
            $max = max($max ?? -50, $frand);
        }

        $this->assertLessThanOrEqual(42, $max);
        $this->assertGreaterThanOrEqual(-42, $min);

        $this->assertGreaterThanOrEqual(1, $max);
        $this->assertLessThanOrEqual(0, $min);
    }

    public function testNrand()
    {
        $nbIterations = 1000;

        // Below -1 sigma
        $sd1 = 0;
        // -1 sigma to 0 sigma
        $sd2 = 0;
        // 0 sigma to 1 sigma
        $sd3 = 0;
        // above 1 sigma
        $sd4 = 0;

        for ($i = $nbIterations; $i; --$i) {
            $nrand = Random::nrand(0, 1);

            if ($nrand < -1) {
                ++$sd1;
            } elseif ($nrand < 0) {
                ++$sd2;
            } elseif ($nrand < 1) {
                ++$sd3;
            } else {
                ++$sd4;
            }
        }

        // Make sure standard deviations are "almost" respected

        // below -1 sigma => 15.9% of the results
        $this->assertLessThanOrEqual($nbIterations / 10, abs($sd1 - ($nbIterations * 0.159)));
        // -1 sigma to 0 sigma => 34.1% of the results
        $this->assertLessThanOrEqual($nbIterations / 10, abs($sd2 - ($nbIterations * 0.341)));
        // 0 sigma to 1 sigma => 34.1% of the results
        $this->assertLessThanOrEqual($nbIterations / 10, abs($sd3 - ($nbIterations * 0.341)));
        // above 1 sigma sigma => 15.9% of the results
        $this->assertLessThanOrEqual($nbIterations / 10, abs($sd4 - ($nbIterations * 0.159)));
    }
}
