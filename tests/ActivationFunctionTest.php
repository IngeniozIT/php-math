<?php
declare(strict_types = 1);

namespace IngeniozIT\Math\Tests;

use PHPUnit\Framework\TestCase;

use IngeniozIT\Math\ActivationFunction;

class ActivationFunctionTest extends TestCase
{
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
}
