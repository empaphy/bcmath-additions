<?php use function empaphy\bcmaph\bcnewton; use PHPUnit\Framework\TestCase;

class NewtonTest extends TestCase
{
    /**
     * @return void
     */
    public function testBcNewtonSingleIteration()
    {
        $scale = 10;

        $f = function($x) use ($scale) {
            return bcsub(bcmul($x, $x, $scale), '2', $scale);
        };

        $fp = function($x) use ($scale) {
            return bcmul('2', $x, $scale);
        };

        $x0 = '1';

        $x1 = bcnewton($f, $fp, $x0, $scale);

        $expected = '1.5000000000';

        $this->assertEquals($expected, $x1);
    }

    /**
     * @return void
     */
    public function testBcNewtonConvergence()
    {
        $scale = 10;

        $f = function($x) use ($scale) {
            return bcsub(bcmul($x, $x, $scale + 5), '2', $scale + 5);
        };

        $fp = function($x) use ($scale) {
            return bcmul('2', $x, $scale + 5);
        };

        $x = '1';

        // Perform multiple iterations
        for ($i = 0; $i < 5; $i++) {
            $x = bcnewton($f, $fp, $x, $scale);
        }

        // Expected root is sqrt(2)
        $expected = bcsqrt('2', $scale);

        // Compute absolute difference
        $difference = bcsub($x, $expected, $scale);
        if (bccomp($difference, '0', $scale) < 0) {
            $difference = bcsub('0', $difference, $scale);
        }

        // Assert difference is within tolerance
        $tolerance = '0.0000001';
        $this->assertTrue(
            bccomp($difference, $tolerance, $scale) < 0,
            "Difference $difference is not within tolerance $tolerance"
        );
    }

    /**
     * @return void
     */
    public function testBcNewtonWithStrings()
    {
        $scale = 10;

        $f = '0.1';
        $fp = '2';
        $x = '1';

        $result = bcnewton($f, $fp, $x, $scale);

        $expected = '0.9500000000';

        $this->assertEquals($expected, $result);
    }
}
