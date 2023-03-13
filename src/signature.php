<?php

declare(strict_types=1);

/**
 * Get the absolute value of a number.
 *
 * @param  string    $num
 * @param  int|null  $scale  Optional number of digits after the decimal point.
 * @return string The absolute value of `$num`.
 */
function bcabs(string $num, ?int $scale = null): string
{
    return bcmul($num, bcsign($num, $scale) . '1', $scale);
}

/**
 * Returns the sign for the given number.
 *
 * @param  string    $num
 * @param  int|null  $scale
 * @return string '-' if the number is negative, an empty string ('') otherwise.
 */
function bcsign(string $num, ?int $scale = null): string
{
    // if num < 0: return '-'
    // else return ''
    return (bccomp($num, '0', $scale) === -1) ? '-' : '';
}
