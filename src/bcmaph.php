<?php

declare(strict_types=1);

namespace empaphy\bcmaph;

/**
 * Get the absolute value of a number.
 *
 * @param  numeric-string    $num
 * @param  int|null  $scale  Optional number of digits after the decimal point.
 * @return numeric-string The absolute value of `$num`.
 */
function bcabs(string $num, int $scale = null): string
{
    if (\bccomp($num, '0', $scale) === -1) {
        // TODO: benchmark `bcsub('0', $num, $scale)` vs `bcmul($num, '-1', $scale)`
        return \bcsub('0', $num, $scale);
    }

    return $num;
}

/**
 * Round fractions up.
 *
 * Returns the next highest integer value by rounding up `$num` if necessary.
 *
 * @param  numeric-string    $num    The value to round, as a string.
 * @param  int|null  $scale  This optional parameter is used to set the number
 *                           of digits after the decimal place in the result. If
 *                           omitted, it will default to the scale set globally
 *                           with the {@see bcscale()} function, or fallback to
 *                           `0` if  this has not been set.
 * @return numeric-string `$num` rounded up to the next highest integer, as a string.
 */
function bcceil(string $num, int $scale = null): string
{
    $numScale = bcgetscale($num);

    // Removes any trailing digits beyond scale.
    $scaled = \bcadd($num, '0', $scale);

    // If $num's scale is already below wat we're `ceil()`ing for,
    // or the 'scaled' $num is identical to num, just return $scaled.
    if ($numScale < $scale || \bccomp($scaled, $num, $numScale) !== -1) {
        return $scaled;
    }

    /**
     * Add a fraction at the last position of the scale.
     *
     * @var numeric-string $correction
     */
    $correction = '0.' . \str_repeat('0', $scale - 1) . '1'; // @phpstan-ignore varTag.nativeType

    return \bcadd($scaled, $correction, $scale);
}

/**
 * Calculate the symmetric difference quotient of `$f($x±$h)` with arbitrary precision.
 *
 * It is defined as:
 *
 *     f(x + h) - f(x - h)
 *     -------------------
 *             2h
 *
 * @template X of numeric-string
 *
 * @param  callable(X $x): numeric-string  $f      An absolute value function f(x) = |x|.
 * @param  X                               $x      Value to calculate the derivative at.
 * @param  numeric-string                  $h      Represents a small change in `$x`.
 * @param  int                             $scale  This optional parameter is used to set the number of digits
 *                                                 after the decimal place in the result. If omitted, it will
 *                                                 default to the scale set globally with the {@link bcscale()}
 *                                                 function, or fallback to 0 if this has not been set.
 * @return numeric-string
 *
 * @throws \DivisionByZeroError
 */
function bcdiff2(callable $f, string $x, string $h, int $scale = null): string
{
    return \bcdiv(
        \bcsub($f(\bcadd($x, $h, $scale)), $f(\bcsub($x, $h, $scale)), $scale),
        \bcmul('2', $h, $scale),
        $scale
    );
}

/**
 * Returns a function that calculates the symmetric difference quotient of `$f($x±$h)` with arbitrary precision.
 *
 * It is defined as:
 *
 *     f(x + h) - f(x - h)
 *     -------------------
 *             2h
 *
 * @template X of numeric-string
 *
 * @param  callable(X $x): numeric-string  $f      An absolute value function f(x) = |x|.
 * @param  numeric-string                  $h      Represents a small change in `$x`.
 * @param  int                     $scale  This optional parameter is used to set the number of digits
 *                                         after the decimal place in the result. If omitted, it will
 *                                         default to the scale set globally with the {@link bcscale()}
 *                                         function, or fallback to 0 if this has not been set.
 * @return callable(X $x): numeric-string
 *
 * @throws \DivisionByZeroError
 */
function bcdiff2fn(callable $f, string $h, int $scale = null): callable
{
    return static function (string $x) use ($f, $h, $scale): string {
        /** @var numeric-string $x */
        $subScale = \max(bcgetscale($x, $h), $scale);

        $xh = \bcsub($x, $h, $subScale);
        $fxh = $f($xh);

        return \bcdiv(
            \bcsub($f(\bcadd($x, $h, $subScale)), $fxh, $subScale),
            \bcmul('2', $h, $subScale),
            $scale
        );
    };
}

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
    $scale = $scale ?? bcgetscale();

    $tolerance = \bcpow('10', (string) -$scale, $scale);
    $min = \bcsub('0', $tolerance, $scale);
    $max = \bcadd('0', $tolerance, $scale);

    $fx = $f($x);

    $i = 0;
    while ((\bccomp($fx, $min, $scale) === -1 || \bccomp($fx, $max, $scale) === 1) && 1000 > $i++) {
        $x = bcnewton($fx, $fp, $x, $scale + 5);
        $fx = $f($x);
    }

    return bcround($x, $scale);
}

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
    return \bcadd($num, '0', $scale);
}

/**
 * Returns the scale value for `$num` based on the number of digits trailing the period (even zeroes), or the global
 * scale value if no number is provided.
 *
 * @param  numeric-string  ...$nums
 * @return int
 */
function bcgetscale(string ...$nums): int
{
    if ([] === $nums) {
        if (\version_compare(PHP_VERSION, '7.3.0') >= 0) {
            /** PHP 7.3.0 and higher return the current scale when calling `bcscale()`.
             * @noinspection PhpParamsInspection
             * @noinspection PhpStrictTypeCheckingInspection */
            return \bcscale();
        }

        $nums[] = \bcdiv('1', '3');
    }

    $scales = [0];
    foreach ($nums as $num) {
        $periodPos = \strpos($num, '.');

        // If there is no period in $num, scale is 0.
        if (false === $periodPos) {
            $scales[] = 0;
        } else {
            // Count the number of digits to deduce what the current scale is.
            $scales[] = \strlen($num) - $periodPos - 1;
        }
    }

    return max($scales);
}

/**
 * Newton's method for finding roots, with arbitrary precision.
 *
 * @template X of numeric-string
 *
 * @param  numeric-string|callable(X $x): numeric-string  $f      A real-valued function `$f($x)`.
 * @param  numeric-string|callable(X $x): numeric-string  $fp     The derivative of `$f`.
 * @param  X                                              $x      A guess for a root of `$f`.
 * @param  int                                            $scale  This optional parameter is used to set the number of
 *                                                                digits after the decimal place in the result. If
 *                                                                omitted, it will default to the scale set globally
 *                                                                with the {@link bcscale()} function, or fallback to
 *                                                                `0` if this has not been set.
 * @return numeric-string
 */
function bcnewton($f, $fp, string $x, int $scale = null): string
{
    if (\is_callable($f)) {
        $f = $f($x);
    }

    if (\is_callable($fp)) {
        $fp = $fp($x);
    }

    return \bcsub($x, \bcdiv($f, $fp, $scale), $scale);
}

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
    if (\is_string($value) && \is_numeric($value)) {
        if (\strpos($value, '.') !== false) {
            if (null !== $precision) {
                return \round((float) $value, $precision, $mode);
            }

            return (float) $value;
        }

        return (int) $value;
    }

    if (\is_float($value) || \is_int($value)) { // @phpstan-ignore function.alreadyNarrowedType
        return $value;
    }

    throw new \InvalidArgumentException("Invalid \$value for bcnormalize(): {$value}");
}

/**
 * Calculate the (`$n`th root) for `$num`.
 *
 * The `n`th root of `num` is a number `r` (the root) which, when raised to the power of the positive integer `n`, yields
 * `num`:
 *
 *     ⁿ√num = r
 *     rⁿ == num
 *
 * @param  numeric-string  $num    The operand, as a string. The result raised to `$n` should yield this number.
 * @param  numeric-string  $n      Power to which the result should be raised to yield `$num`, as a string.
 * @param  int|null        $scale  Used to set the number of digits after the decimal place in the result. If omitted,
 * 　　　　　　　　　　　　　　　　　　　it will　default to the scale set globally with the `bcscale()` function, or fallback
 * 　　　　　　　　　　　　　　　　　　　to 0 if this　has not been set.
 * @return string The decimal approximation of the $nth root of `$num` as a string with `$scale` decimal places.
 */
function bcroot(string $num, string $n = '2', int $scale = null): string
{
    // @todo If $n is a float, use {@see bcsurd()}.

    if (\bccomp($n, '0', $scale) === 0) {
        throw new \DivisionByZeroError("Unable to calculate ⁰√{$num}: can't calculate 0th root");
    }

    // if n == 1 or num == 1
    if (\bccomp($n, '1', $scale) === 0 || \bccomp($num, '1', $scale) === 0) {
        return \bcadd($num, '0', $scale); // Return same with the correct scale.
    }

    // if n == 2
    if (\bccomp($n, '2', $scale) === 0) {
        /** @noinspection PhpStrictTypeCheckingInspection */
        return \bcsqrt($num, $scale);
    }

    // x0 = num⁽¹⁄ⁿ⁾
    $x = (string) (((float) $num) ** (1 / (float) $n));

    $tolerance = \bcpow('10', (string) -$scale, $scale);
    $min = \bcsub('0', $tolerance, $scale);
    $max = \bcadd('0', $tolerance, $scale);

    // f(r) = rⁿ - num
    $fx = \bcsub(\bcpow($x, $n, $scale), $num, $scale);

    $i = 0;
    while ((\bccomp($fx, $min, $scale) === -1 || \bccomp($fx, $max, $scale) === 1) && 1000 > $i++) {
        // x = x - f(x) / n × x⁽ⁿ⁻¹⁾
        $x = \bcsub(
            $x,
            \bcdiv($fx, \bcmul($n, \bcpow($x, \bcsub($n, '1', $scale + 5), $scale + 5), $scale + 5), $scale + 5),
            $scale + 5
        );

        // f(r) = rⁿ - num
        $fx = \bcsub(\bcpow($x, $n, $scale), $num, $scale);;
    }

    return bcround($x, $scale);
}

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
    if (null === $scale) {
        $scale = bcgetscale();
    }

    /**
     * Create a correction fraction based on the scale.
     *
     * @var numeric-string $correction
     */
    $correction = bcsign($num, $scale) . '0.' . \str_repeat('0', $scale) . '5'; // @phpstan-ignore varTag.nativeType

    // Add the correction to the original number
    $num = \bcadd($num, $correction, $scale + 1);

    // Use `bcadd()` which will round down the result
    return \bcadd($num, '0', $scale);
}

/**
 * Raise an arbitrary precision number to a fraction.
 *
 * @param  numeric-string  $num                The base, as a string.
 * @param  int             $exponent_dividend  The fractional exponent dividend.
 * @param  int             $exponent_divisor   The fractional exponent divisor.
 * @param  int|null        $scale              This optional parameter is used to set the number of digits after the decimal
 *                                             place in the result. If omitted, it will default to the scale set globally with
 *                                             the {@link bcscale()} function, or fallback to 0 if this has not been set.
 * @return string The result as a string.
 */
function bcsurd(string $num, int $exponent_dividend, int $exponent_divisor, int $scale = null): string
{
    // To calculate the power of a fraction, you can use the following formula:
    //
    //     numᵇ/ᶜ = (a¹/ᶜ)ᵇ
    //     num¹/ᶜ = ᶜ√a
    //
    // So we use our `bcroot()` function to calculate `ᶜ√num` and then raise
    // that to `b`, where `b` is $exponent_dividend and `c` is $exponent_divisor.
    $nth = bcroot($num, (string) $exponent_divisor, $scale ? $scale + 2 : null);

    return bcpow($nth, (string) $exponent_dividend, $scale); // @phpstan-ignore argument.type
}

/**
 * Returns the sign for the given number.
 *
 * @param  numeric-string  $num
 * @param  int|null        $scale
 * @return "-" | "" '-' if the number is negative, an empty string ('') otherwise.
 */
function bcsign(string $num, int $scale = null): string
{
    // if num < 0: return '-'
    // else return ''
    return (\bccomp($num, '0', $scale) === -1) ? '-' : '';
}
