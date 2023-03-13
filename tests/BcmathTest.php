<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class BcmathTest extends TestCase
{
    /**
     * @return void
     */
    public function testBcgetscale(): void
    {
        bcscale(37);
        $this->assertEquals(37, bcgetscale());

        bcscale(0);
        $this->assertEquals(0, bcgetscale());

        $this->assertEquals(0, bcgetscale('17'));
        $this->assertEquals(5, bcgetscale('23.00000'));
        $this->assertEquals(7, bcgetscale('23.0000003'));
    }
}
