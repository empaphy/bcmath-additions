<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcgetscale;

final class GetscaleTest extends TestCase
{
    /**
     * @return void
     */
    public function test_bcgetscale()
    {
        bcscale(37);
        $this->assertEquals(37, bcgetscale());

        bcscale(0);
        $this->assertEquals(0, bcgetscale());

        $this->assertEquals(0, bcgetscale('17'));
        $this->assertEquals(5, bcgetscale('23.00000'));
        $this->assertEquals(7, bcgetscale('23.0000003'));

        $this->assertEquals(6, bcgetscale('23.000003', '23.00000'));
        $this->assertEquals(8, bcgetscale('23.000003', '23.00000003'));
    }
}
