<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcround;

final class RoundTest extends TestCase
{
    public static function bcround_data_provider(): array
    {
        return [
            [ '0',      '0',      0],
            [ '0',     '-0',      0],
            [ '2',      '1.5',    0],
            [ '1',      '1.4',    0],
            [ '1',      '1',      0],
            ['-1',     '-1',      0],
            [ '0.000',  '0.0004', 3],
            [ '0.000', '-0.0004', 3],
            [ '1.000',  '1.0004', 3],
            ['-1.000', '-1.0004', 3],
            [ '1.001',  '1.0007', 3],
            ['-1.001', '-1.0007', 3],
        ];
    }

    /**
     * @dataProvider bcround_data_provider
     *
     * @param string $expected
     * @param string $num
     * @param int $scale
     * @return void
     */
    public function test_bcround(string $expected, string $num, int $scale)
    {
        $this->assertEquals($expected, bcround($num, $scale));
    }
}
