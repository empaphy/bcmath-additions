<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcsin;

final class SinTest extends TestCase
{
    /**
     * @return array<int, array{0: numeric-string, 1: int, 2: numeric-string}>
     */
    public static function bcsin_data_provider(): array
    {
        return [
            ['0.52359878', 8, '0.50000000'],
            ['0.78539816', 4, '0.7071'],
            ['1',          8, '0.84147098'],
            ['1.04719755', 4, '0.8660'],
            ['1.57079633', 8, '1.00000000'],
            [(string) (1 / 6 * M_PI), 13, '0.5000000000000'],
            [(string) (1 / 4 * M_PI), 13, '0.7071067811865'],
        ];
    }

    /**
     * @dataProvider bcsin_data_provider
     *
     * @param  numeric-string  $num
     * @param  int             $scale
     * @param  numeric-string  $expected
     * @return void
     */
    public function test_bcsin(string $num, int $scale, string $expected)
    {
        $this->assertEquals($expected, bcsin($num, $scale));
    }
}
