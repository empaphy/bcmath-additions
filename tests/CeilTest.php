<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcceil;
use const empaphy\bcmaph\BC_MAX_SCALE;

final class CeilTest extends TestCase
{
    /**
     * @return array<array{0: numeric-string, 1: int<0,2147483647>|null, 2: numeric-string}>
     */
    public static function bcceil_data_provider(): array
    {
        return [
             0 => [ '0',         0,  '0' ],
             1 => ['-0',         0, '-0' ],
             2 => [ '1',         0,  '1' ],
             3 => ['-1',         0, '-1' ],
             4 => [ '0.1234',    4,  '1' ],
             5 => [ '0.1234',    3,  '1' ],
             6 => [ '0.0004',    4,  '1' ],
             7 => [ '0.0004',    3,  '0' ],
             8 => ['-0.1234',    4, '-1' ],
             9 => ['-0.1234',    3, '-1' ],
            10 => ['-0.0004',    4, '-1' ],
            11 => ['-0.0004',    3, '-0' ],
        ];
    }

    /**
     * @return array<string, array{0: negative-int|int<2147483648,2147483648>}>
     */
    public static function invalid_scales(): array
    {
        return [
            'negative scale'                 => [-1],
            'scale greater than BC_MA_SCALE' => [BC_MAX_SCALE + 1],
        ];
    }

    /**
     * @dataProvider bcceil_data_provider
     *
     * @param  numeric-string          $num       The value to round, as a string.
     * @param  int<0,2147483647>|null  $scale     Set the number of digits after the decimal place in the result.
     * @param  numeric-string          $expected  The expected value.
     * @return void
     */
    public function test_bcceil(string $num, ?int $scale, string $expected): void
    {
        $this->assertEquals($expected, bcceil($num, $scale));
    }

    public function test_large_scale(): void
    {
        $this->assertEquals('0', bcceil(self::scaled_number('0', BC_MAX_SCALE - 1, '1'), BC_MAX_SCALE - 1));
        $this->assertEquals('1', bcceil(self::scaled_number('0', BC_MAX_SCALE - 1, '1'), BC_MAX_SCALE));
    }

    /**
     * @param  numeric-string  $num
     * @param  positive-int    $scale
     * @param  string          $suffix
     * @return numeric-string
     */
    private static function scaled_number(string $num, int $scale, string $suffix = '1'): string
    {
        $intLength = strlen($num);
        $numLength = $intLength + 1 + $scale;
        $num = str_repeat('0', $numLength);
        $num[$intLength] = '.';
        $num .= $suffix;

        return $num; // @phpstan-ignore return.type
    }

    /**
     * @dataProvider invalid_scales
     *
     * @param  negative-int|int<2147483648,2147483648>  $scale
     * @return void
     */
    public function test_value_error(int $scale): void
    {
        $this->expectException(\ValueError::class);
        bcceil('0.1234', $scale); // @phpstan-ignore argument.type
    }
}
