<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcfloor;

final class FloorTest extends TestCase
{
    public static function bcfloor_data_provider(): array
    {
        return [
            ['0',       '0',     0],
            ['0',      '-0',     0],
            ['1',       '1',     0],
            ['-1',     '-1',     0],
            [ '0.000',  '0.0007', 3],
            [ '0.000', '-0.0007', 3],
            [ '1.000',  '1.0007', 3],
            ['-1.000', '-1.0007', 3],
        ];
    }

    /**
     * @dataProvider bcfloor_data_provider
     *
     * @param  string      $expected
     * @param  string      $num
     * @param  null | int  $scale
     * @return void
     */
    public function test_bcfloor(string $expected, string $num, int $scale = null)
    {
        $this->assertEquals($expected, bcfloor($num, $scale));
    }
}
