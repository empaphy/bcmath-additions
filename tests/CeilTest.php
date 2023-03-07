<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcceil;
use function empaphy\bcmaph\bcfloor;

final class CeilTest extends TestCase
{
    /**
     * @return array<array{0: string, 1: string, 2: int}>
     */
    public static function bcceil_data_provider(): array
    {
        return [
             0 => [ '0',      '0',      0],
             1 => [ '0',     '-0',      0],
             2 => [ '1',      '1',      0],
             3 => ['-1',     '-1',      0],
             4 => [ '0.001',  '0.0004', 3],
             5 => [ '0.000', '-0.0004', 3],
             6 => [ '1.001',  '1.0004', 3],
             7 => ['-1.000', '-1.0004', 3],
             8 => [ '0.001',  '0.0007', 3],
             9 => [ '0.000', '-0.0007', 3],
            10 => [ '1.001',  '1.0007', 3],
            11 => ['-1.000', '-1.0007', 3],
        ];
    }

    /**
     * @dataProvider bcceil_data_provider
     *
     * @param  string      $expected  The expected value.
     * @param  string      $num       The value to round, as a string.
     * @param  null | int  $scale     Set the number of digits after the decimal place in the result.
     * @return void
     */
    public function test_bcceil(string $expected, string $num, int $scale)
    {
        $this->assertEquals($expected, bcceil($num, $scale));
    }
}
