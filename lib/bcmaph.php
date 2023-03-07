<?php

declare(strict_types=1);

use function empaphy\bcmaph\bcsign;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'bcmaph.php';

if (! function_exists('bcabs')) {

    /**
     * Get the absolute value of a number.
     *
     * @param numeric-string $num
     * @param int|null $scale Optional number of digits after the decimal point.
     * @return numeric-string The absolute value of `$num`.
     */
    function bcabs(string $num, int $scale = null): string
    {
        return \empaphy\bcmaph\bcabs($num, $scale);
    }

}

if (! function_exists('bcceil')) {

    /**
     * Round fractions up.
     *
     * Returns the next highest integer value by rounding up `$num` if necessary.
     *
     * @param  numeric-string  $num    The value to round, as a string.
     * @param  int | null      $scale  This optional parameter is used to set the number of digits after the decimal
     *                                 place in the result. If omitted, it will default to the scale set globally with
     *                                 the {@see bcscale()} function, or fallback to `0` if  this has not been set.
     * @return numeric-string `$num` rounded up to the next highest integer, as a string.
     */
    function bcceil(string $num, int $scale = null): string
    {
        return \empaphy\bcmaph\bcceil($num, $scale);
    }

}

if (! function_exists('bcdiff2')) {

    /**
     * Calculate the symmetric difference quotient of `$f($x±$h)` with arbitrary precision.
     *
     * @template X of numeric-string
     *
     * @param  callable(X $x): numeric-string  $f      An absolute value function f(x) = |x|.
     * @param  X                               $x      Value to calculate the derivative at.
     * @param  numeric-string                  $h      Represents a small change in `$x`.
     * @param  int                             $scale  This optional parameter is used to set the number of digits after
     *                                                 the decimal place in the result. If omitted, it will default to
     *                                                 the scale set globally with the {@link bcscale()} function, or
     *                                                 fallback to 0 if this has not been set.
     * @return numeric-string
     *
     * @throws \DivisionByZeroError
     */
    function bcdiff2(callable $f, string $x, string $h, int $scale = null): string
    {
        return \empaphy\bcmaph\bcdiff2($f, $x, $h, $scale);
    }

}

if (! function_exists('bcdiff2fn')) {

    /**
     * Returns a function that calculates the symmetric difference quotient of `$f($x±$h)` with arbitrary precision.
     *
     * @template X of numeric-string
     *
     * @param  callable(X $x): numeric-string  $f      An absolute value function f(x) = |x|.
     * @param  numeric-string                  $h      Represents a small change in `$x`.
     * @param  int                             $scale  This optional parameter is used to set the number of digits after
     *                                                 the decimal place in the result. If omitted, it will default to
     *                                                 the scale set globally with the {@link bcscale()} function, or
     *                                                 fallback to 0 if this has not been set.
     * @return callable(X $x): numeric-string
     *
     * @throws \DivisionByZeroError
     */
    function bcdiff2fn(callable $f, string $h, int $scale = null): callable
    {
        return \empaphy\bcmaph\bcdiff2fn($f, $h, $scale);
    }

}

if (! function_exists('bcfindroot')) {

    /**
     * Finds the value of `$x` for which `$f($x)` is closest to 0 using Newton's method.
     *
     * @template X of numeric-string
     *
     * @param  callable(X $x): numeric-string  $f      The function to find the root of.
     * @param  callable(X $x): numeric-string  $fp     The derivative of `$f`.
     * @param  X                               $x      Initial guess for the root.
     * @param  int                             $scale
     * @return numeric-string
     */
    function bcfindroot(callable $f, callable $fp, string $x = '0.5', int $scale = null): string
    {
        return \empaphy\bcmaph\bcfindroot($f, $fp, $x, $scale);
    }

}

if (! function_exists('bcfloor')) {

    /**
     * Round fractions down.
     *
     * Returns the next lowest integer value (as float) by rounding down num if
     * necessary.
     *
     * @param  numeric-string    $num    The numeric value to round, as a string.
     * @param  int|null  $scale  This optional parameter is used to set the number
     *                           of digits after the decimal place in the result. If
     *                           omitted, it will default to the scale set globally
     *                           with the {@see bcscale()} function, or fallback to
     *                           `0` if this has not been set.
     * @return numeric-string `$num` rounded to the next lowest integer, as a string.
     */
    function bcfloor(string $num, int $scale = null): string
    {
        return \empaphy\bcmaph\bcfloor($num, $scale);
    }

}

if (! function_exists('bcgetscale')) {

    /**
     * Get the scale of the given number.
     *
     * @param  numeric-string|null  $num
     * @return int
     */
    function bcgetscale(string $num = null): int
    {
        return \empaphy\bcmaph\bcgetscale($num);
    }

}

if (! function_exists('bcnewton')) {

    /**
     * Newton's method for finding roots, with arbitrary precision.
     *
     * @template X of numeric-string
     *
     * @param  numeric-string|callable(X $x): numeric-string  $f      A real-valued function `$f($x)`.
     * @param  numeric-string|callable(X $x): numeric-string  $fp     The derivative of `$f`.
     * @param  X                                              $x      A guess for a root of `$f`.
     * @param  int                                            $scale  This optional parameter is used to set the number
     *                                                                of digits after the decimal place in the result.
     *                                                                If omitted, it will default to the scale set
     *                                                                globally with the {@link bcscale()} function, or
     *                                                                fallback to `0` if this has not been set.
     * @return numeric-string
     */
    function bcnewton($f, $fp, string $x, int $scale = null): string
    {
        return \empaphy\bcmaph\bcnewton($f, $fp, $x, $scale);
    }

}

if (! function_exists('bcnormalize')) {

    /**
     * Normalizes a numeric string to an integer or float, as appropriate.
     *
     * @template TNumber of (float | int)
     *
     * @param  numeric-string | TNumber  $value
     * @param  int                       $precision
     * @param  int<1,4>                  $mode
     * @return ($value is TNumber ? TNumber : float | int)
     */
    function bcnormalize($value, int $precision = null, int $mode = PHP_ROUND_HALF_UP)
    {
        return \empaphy\bcmaph\bcnormalize($value, $precision, $mode);
    }

}

if (! function_exists('bcroot')) {

    /**
     * Calculate the (`$n`th root) for $num.
     *
     * The nth root of `num` is a number `r` (the root) which, when raised to the power of the positive integer `n`, yields
     * `num`:
     *
     *     rⁿ == num
     *
     * By default, the square root is calculated.
     *
     * @param  numeric-string   $num    The operand, as a string. The result raised to `$n` should yield this number.
     * @param  numeric-string   $n      Power to which the result should be raised to yield `$num`, as a string.
     * @param  int|null         $scale  Used to set the number of digits after the decimal place in the result. If omitted, it will
     *                                  default to the scale set globally with the `bcscale()` function, or fallback to 0 if this
     *                                  has not been set.
     * @return string The decimal approximation of the $nth root of `$num` as a string with `$scale` decimal places.
     */
    function bcroot(string $num, string $n = '2', int $scale = null): string
    {
        return \empaphy\bcmaph\bcroot($num, $n, $scale);
    }

}

if (! function_exists('bcround')) {

    /**
     * Rounds an arbitrary precision decimal number.
     *
     * Returns the rounded value of num to the precision specified with `$scale`.
     *
     * @param  numeric-string    $num    The value to round, as a string.
     * @param  int|null  $scale  This optional parameter is used to set the number
     *                           of digits after the decimal place in the result. If
     *                           omitted, it will default to the scale set globally
     *                           with the {@see bcscale()} function, or fallback to
     *                           0 if this has not been set.
     * @return numeric-string The rounded number, as a string.
     */
    function bcround(string $num, int $scale = null): string
    {
        return \empaphy\bcmaph\bcround($num, $scale);
    }

}

if (! function_exists('bcsign')) {

    /**
     * Returns the sign for the given number.
     *
     * @param  numeric-string  $num
     * @param  int|null        $scale
     * @return "-" | "" '-' if the number is negative, an empty string ('') otherwise.
     */
    function bcsign(string $num, int $scale = null): string
    {
        return \empaphy\bcmaph\bcsign($num, $scale);
    }

}
