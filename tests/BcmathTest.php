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

    /**
     * @dataProvider bcapproxDataProvider
     *
     * @param  string    $expected
     * @param  string    $target
     * @param  string    $min
     * @param  string    $max
     * @param  callable  $test
     * @param  int|null  $scale
     * @return void
     */
    public function testBcapprox(
        string $expected,
        string $target,
        string $min,
        string $max,
        callable $test,
        ?int   $scale
    ): void {
        $actual = bcapprox($target, $min, $max, $test, $scale);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider bcrootDataProvider
     *
     * @param  string    $expected
     * @param  string    $num
     * @param  string    $n
     * @param  int|null  $scale
     * @return void
     */
    public function testBcroot(string $expected, string $num, string $n, ?int $scale): void
    {
        $actual = bcroot($num, $n, $scale);
        $this->assertEquals($expected, $actual);
    }

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

    public function bcapproxDataProvider(): array
    {
        $data = [];

        for ($i = 0; $i < 29; $i++) {
            $data[] = [
                (string) $i,                      // expected
                (string) ($i + 7),                // target (expected + 7)
                '0',                              // min
                '20',                             // max
                function($g) { return bcadd($g, '7'); }, // test
                0,
            ];
        }

        return [
            [
                '1.160808205961694',                          // expected
                '17',                                         // target
                '0',                                          // min
                '2',                                          // max
                function($g) { return bcpow($g, '19', 15); }, // test
                15,                                           //scale
            ] + $data,
        ];
    }

    public function bcrootDataProvider(): array
    {
        return [
            [
                '1.160808205961694',
                '17',
                '19',
                15,
            ],
            [
                '1.0844572045864763396830684847861137767791284855',
                '7',
                '24',
                46,
            ],
            [
                '1.0000000',
                '1',
                '7',
                7,
            ],
            [
                '13.00000',
                '13',
                '1',
                5,
            ],
            [
                '1.084457204586476339683068484786113776779128485499972439179533144286867274051399621715070401611661590895980090316271185372465974063',
                '7',
                '24',
                129,
            ],
        ];
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
