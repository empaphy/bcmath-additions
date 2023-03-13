<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApproxTest extends TestCase
{
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
}
