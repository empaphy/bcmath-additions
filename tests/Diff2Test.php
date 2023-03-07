<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcdiff2;
use function empaphy\bcmaph\bcdiff2fn;

final class Diff2Test extends TestCase
{
    /**
     * @return array<int, array{0: callable, 1: string, 2: string, 3: int, 4: string}>
     */
    public static function bcdiff2_data_provider(): array
    {
        return [
            0 => [function (string $x): string { return bcpow($x, '2'); }, '1', '0.1', 3, '5.000'],
        ];
    }

    /**
     * @dataProvider bcdiff2_data_provider
     *
     * @template X of string
     *
     * @param  callable(X $x): string  $f         An absolute value function f(x) = |x|.
     * @param  X                       $x         Value to calculate the derivative at.
     * @param  string                  $h         Represents a small change in `$x`.
     * @param  int                     $scale     Set the number of digits after the decimal place in the result.
     * @param  string                  $expected
     * @return void
     */
    public function test_bcdiff2(callable $f, string $x, string $h, int $scale, string $expected)
    {
        $this->assertEquals($expected, bcdiff2($f, $x, $h, $scale));
    }

    /**
     * @dataProvider bcdiff2_data_provider
     *
     * @template X of string
     *
     * @param  callable(X $x): string  $f         An absolute value function f(x) = |x|.
     * @param  X                       $x         Value to calculate the derivative at.
     * @param  string                  $h         Represents a small change in `$x`.
     * @param  int                     $scale     Set the number of digits after the decimal place in the result.
     * @param  string                  $expected
     * @return void
     */
    public function test_bcdiff2fn(callable $f, string $x, string $h, int $scale, string $expected)
    {
        $this->assertEquals($expected, (bcdiff2fn($f, $h, $scale))($x));
    }
}
