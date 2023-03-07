<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcabs;

final class AbsTest extends TestCase
{
    public static function bcabs_data_provider(): array
    {
        return [
            ['0',  '0', 0],
            ['0', '-0', 0],
            ['1',  '1', 0],
            ['1', '-1', 0],
            ['0.000',  '0.000', 3],
            ['0.000', '-0.000', 3],
            ['1.000',  '1.000', 3],
            ['1.000', '-1.000', 3],
        ];
    }

    /**
     * @dataProvider bcabs_data_provider
     *
     * @return void
     */
    public function test_bcabs(string $expected, string $num, int $scale)
    {
        $this->assertEquals($expected, bcabs($num, $scale));
    }
}