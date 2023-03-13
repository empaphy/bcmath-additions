<?php

declare(strict_types=1);

/**
 * Raise an arbitrary precision number to a fraction.
 *
 * @param  string    $num                The base, as a string.
 * @param  int       $exponent_dividend  The fractional exponent dividend.
 * @param  int       $exponent_divisor   The fractional exponent divisor.
 * @param  int|null  $scale              This optional parameter is used to set the number of digits after the decimal
 *                                       place in the result. If omitted, it will default to the scale set globally with
 *                                       the {@link bcscale()} function, or fallback to 0 if this has not been set.
 * @return string The result as a string.
 */
function bcpow_fraction(string $num, int $exponent_dividend, int $exponent_divisor, ?int $scale = null): string
{
    // To calculate the power of a fraction, you can use the following formula:
    //
    //     numᵇ/ᶜ = (a¹/ᶜ)ᵇ
    //     num¹/ᶜ = ᶜ√a
    //
    // So we use our `bcroot()` function to calculate `ᶜ√num` and then raise
    // that to `b`, where `b` is $exponent_dividend and `c` is $exponent_divisor.
    $nth = bcroot($num, (string) $exponent_divisor, $scale ? $scale + 2 : null);

    return bcpow($nth, (string) $exponent_dividend, $scale);
}
