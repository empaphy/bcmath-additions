<?php

declare(strict_types=1);

/**
 * Round fractions up.
 *
 * Returns the next highest integer value by rounding up `$num` if necessary.
 *
 *
 *
 * @param  string    $num    The value to round, as a string.
 * @param  int|null  $scale  This optional parameter is used to set the number
 *                           of digits after the decimal place in the result. If
 *                           omitted, it will default to the scale set globally
 *                           with the {@see bcscale()} function, or fallback to
 *                           `0` if  this has not been set.
 * @return string `$num` rounded up to the next highest integer, as a string.
 */
function bcceil(string $num, ?int $scale = null): string
{
    $numScale = bcgetscale($num);

    // Removes any trailing digits beyond scale.
    $scaled = bcadd($num, '0', $scale);

    // If $num's scale is already below wat we're `ceil()`ing for,
    // or the 'scaled' $num is identical to num, just return $scaled.
    if ($numScale < $scale || bccomp($scaled, $num, $numScale) === 0) {
        return $scaled;
    }

    // Add a fraction at the last position of the scale.
    $correction = bcsign($num, $scale) . '0.' . str_repeat('0', $scale - 1) . '1';

    return bcadd($scaled, $correction, $scale);
}

/**
 * Round fractions down.
 *
 * Returns the next lowest integer value (as float) by rounding down num if
 * necessary.
 *
 * @param  string    $num    The numeric value to round, as a string.
 * @param  int|null  $scale  This optional parameter is used to set the number
 *                           of digits after the decimal place in the result. If
 *                           omitted, it will default to the scale set globally
 *                           with the {@see bcscale()} function, or fallback to
 *                           `0` if this has not been set.
 * @return string `$num` rounded to the next lowest integer, as a string.
 */
function bcfloor(string $num, ?int $scale = null): string
{
    return bcadd($num, '0', $scale);
}

/**
 * Rounds an arbitrary precision decimal number.
 *
 * Returns the rounded value of num to the precision specified with `$scale`.
 *
 * @param  string    $num    The value to round, as a string.
 * @param  int|null  $scale  This optional parameter is used to set the number
 *                           of digits after the decimal place in the result. If
 *                           omitted, it will default to the scale set globally
 *                           with the {@see bcscale()} function, or fallback to
 *                           0 if this has not been set.
 * @return string The rounded number, as a string.
 */
function bcround(string $num, int $scale = null): string
{
    if (null === $scale) {
        $scale = bcgetscale();
    }

    // Create a correction fraction based on the scale.
    $correction = bcsign($num, $scale) . '0.' . str_repeat('0', $scale) . '5';

    // Add the correction to the original number
    $num = bcadd($num, $correction, $scale + 1);

    // Use `bcadd()` which will round down the result
    return bcadd($num, '0', $scale);
}
