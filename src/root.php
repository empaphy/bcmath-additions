<?php

/**
 * @noinspection PhpUnnecessaryCurlyVarSyntaxInspection
 */

declare(strict_types=1);

/**
 * Calculate the (nth root) for $num.
 *
 * An nth root of a number x is a number g which, when raised to the power n, yields x:
 *
 *     gⁿ == x
 *
 * By default, the square root is calculated.
 *
 * @param  string    $num    The operand, as a string. The result raised to `$n` should yield this number.
 * @param  string    $n      Power to which the result should be raised to yield `$num`, as a string.
 * @param  int|null  $scale  Used to set the number of digits after the decimal place in the result. If omitted, it will
 *                           default to the scale set globally with the `bcscale()` function, or fallback to 0 if this
 *                           has not been set.
 * @return string|null The decimal approximation of the $n'th root of `$num` as a string with `$scale` decimal places.
 */
function bcroot(string $num, string $n = '2', ?int $scale = null): ?string {
    switch (bccomp($n, '0', $scale)) {
        case 0:
            throw new DivisionByZeroError("Unable to calculate ⁰√{$num}: can't calculate 0th root");
        case -1:
            return null;
    }

    // if n == 1 or num == 1
    if (bccomp($n, '1', $scale) === 0 || bccomp($num, '1', $scale) === 0) {
        return bcadd($num, '0', $scale); // Return same with the correct scale.
    }

    // if n == 2
    if (bccomp($n, '2', $scale) === 0) {
        /** @noinspection PhpStrictTypeCheckingInspection */
        return bcsqrt($num, $scale);
    }

    // With the above cases out of the way, now we need to find the value `r` where `rⁿ == num`.
    // First we find the integer range in which the value `r` should be.
    $n_min = '0';
    $n_max = '2';

    // while (n_maxⁿ < num): n_max = 2n_max
    while (bccomp(bcpow($n_max, $n, $scale), $num, $scale) === -1) {
        $n_max = bcmul($n_max, '2', $scale);
    }

    $n_max_sub = bcsub($n_max, '1', $scale);

    // while (n_maxⁿ > num): n_max = n_max - 1
    while (bccomp(bcpow($n_max_sub, $n, $scale), $num, $scale) === 1) {
        $n_max     = $n_max_sub;
        $n_max_sub = bcsub($n_max, '1', $scale);
    }

    // Call bcapprox to approximate r.
    return bcapprox(
        $num,
        $n_min,
        $n_max,
        static function($r) use ($scale, $n) {
            return bcpow($r, $n, $scale);
        },
        $scale
    );
}
