<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcsign;

final class SignTest extends TestCase
{
    public static function bcsign_data_provider(): array
    {
        return [
            ['',   '1', 0],
            ['',   '0', 0],
            ['',  '-0', 0],
            ['-', '-1', 0],
        ];
    }

    /**
     * @dataProvider bcsign_data_provider
     *
     * @return void
     */
    public function test_bcsign(string $expected, string $num, int $scale = null)
    {
        $this->assertEquals($expected, bcsign($num, $scale));
    }
}
