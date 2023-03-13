<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RaiseTest extends TestCase
{
    /**
     * @dataProvider bcpowFractionDataProvider
     *
     * @param  string    $expected
     * @param  string    $num
     * @param  int       $exponent_dividend
     * @param  int       $exponent_divisor
     * @param  int|null  $scale
     * @return void
     */
    public function testBcpowFraction(
        string $expected,
        string $num,
        int $exponent_dividend,
        int $exponent_divisor,
        ?int $scale
    ): void {
        $actual = bcpow_fraction($num, $exponent_dividend, $exponent_divisor, $scale);
        $this->assertEquals($expected, $actual);
    }

    public function bcpowFractionDataProvider(): array
    {
        return [
            [
                '2.2496977985490376600089590261564167504910892977907254302893228416568713917727522568893790292826839554572679025185864184214744970', // expected
                '7', // num
                10,  // exponent_dividend
                24,  // exponent_divisor
                127, // scale
            ],
        ];
    }
}
