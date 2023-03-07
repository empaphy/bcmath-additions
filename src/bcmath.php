<?php /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

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

/**
 * Search a value between $min and $max, which when provided to $callable() returns $target.
 *
 * @param  mixed     $target     Result we're trying to find.
 * @param  string    $min        Minimum guess value to try.
 * @param  string    $max        Maximum guess value to try.
 * @param  callable  $callable   fn(string $guess): string
 * @param  int|null  $scale      This optional parameter is used to set the number of digits after the decimal place in
 *                               the result. If omitted, it will default to the scale set globally with the
 *                               {@link bcscale()} function, or fallback to 0 if this has not been set.
 * @param  int|null  $precision  Precision to use for approximation steps.
 * @return string  The input value that gave the closest approximation.
 */
function bcapprox(
    $target,
    string $min,
    string $max,
    callable $callable,
    ?int $scale = null,
    ?int $precision = null
): string {
    $stepScale = $precision ?? $scale;

    $minStep = $stepScale ? bcdiv('1', bcpow('10', (string) $stepScale), $stepScale) : 1;
    $subStep = $stepScale ? bcdiv('1', bcpow('10', (string) ($stepScale - 1)), $stepScale) : 10;

    // step = (max - min) / 2
    $step = bcdiv(bcsub($max, $min, $stepScale), '2', $stepScale);

    // we start at min + step, basically in the middle of our range.
    $guess = bcadd($min, $step, $stepScale);
    $closestGuess = $guess;
    $closestDiff  = null;

    $startSubtracting = false;

    do {
        if ($startSubtracting) {
            $step = bcsub($step, $minStep, $stepScale);
        } else {
            $step = bcdiv($step, '2', $stepScale);

            $actualStepScale = bcgetscale(rtrim($step, '0'));
            $mod = bcmod($step, '1', $stepScale);
            if ($stepScale && $actualStepScale >= $stepScale && bccomp($mod, $subStep, $stepScale) !== 1) {
                $startSubtracting = true;
            }
        }

        $result = bcround($callable($guess), $scale);

        // if (abs(result - target) < abs(closestResult - target))
        $diff = bcabs(bcsub($result, $target, $stepScale), $stepScale);
        if (null === $closestDiff || bccomp($diff, $closestDiff, $stepScale) === -1) {
            $closestGuess = $guess;
            $closestDiff = $diff;
        }

        switch (bccomp($result, $target, $stepScale)) {
            case -1: // Guess is too low, increase it by step.
                $guess = bcadd($guess, $step, $stepScale);
                break;

            case 1: // Guess is too high, reduce it by step.
                $guess = bcsub($guess, $step, $stepScale);
                break;

            case 0:
                return bcround($closestGuess, $scale);

            default:
                throw new RuntimeException("bccomp() returned an unexpected value");
        }
    // TODO: check if the result is no longer changing.
    } while (bccomp($step, '0', $stepScale) === 1); // while step > 0

    return bcround($closestGuess, $scale);
}

/**
 * Normalizes a numeric string to an integer or float, as appropriate.
 *
 * @param  mixed     $value
 * @param  int|null  $precision
 * @return int|float
 *
 * @todo Use $precision to round numbers.
 */
function bcnormalize($value, ?int $precision = null)
{
    if (is_string($value) && is_numeric($value)) {
        if (strpos($value, '.') !== false) {
            // TODO: how are values rounded?
            return (float) $value;
        }

        return (int) $value;
    }

    if (is_float($value) || is_int($value)) {
        return $value;
    }

    throw new RuntimeException("Invalid value for bcnormalize(): {$value}");
}

/**
 * @param  float|string[]    $vector
 * @param  float|string[][]  $matrix
 * @return string[]
 */
function bcmul_matrix(array $vector, array $matrix, ?int $scale = null): array
{
    /** @var  string[] $result */
    $result = array_fill_keys(array_keys($matrix), '0');

    foreach ($matrix as $c => $multipliers) {
        foreach ($multipliers as $d => $multiplier) {
            $result[$c] = bcadd($result[$c], bcmul((string) $vector[$d], (string) $multiplier, $scale), $scale);
        }
    }

    return $result;
}

/**
 * Round fractions up.
 *
 * @param  string    $num
 * @param  int|null  $scale
 * @return string
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
 * @param  string    $num
 * @param  int|null  $scale
 * @return string
 */
function bcfloor(string $num, ?int $scale = null): string
{
    return bcadd($num, '0', $scale);
}

/**
 * @param  string    $num
 * @param  int|null  $scale
 * @return string
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

/**
 * Returns the scale value for a number based on the number of digits trailing
 * the period (even zeroes), or the global scale value if no number is provided.
 *
 * @param  string|null  $num
 * @return int
 */
function bcgetscale(?string $num = null): int
{
    if (null === $num) {
        if (version_compare(PHP_VERSION, '7.3.0') >= 0) {
            /** PHP 7.3.0 and higher return the current scale when calling `bcscale()`.
             * @noinspection PhpParamsInspection
             * @noinspection PhpStrictTypeCheckingInspection */
            return bcscale();
        }

        /** Get a sample number.
         * @noinspection CallableParameterUseCaseInTypeContextInspection */
        $num = bcsqrt('2');

        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (null === $num) {
            throw new RuntimeException("Got invalid value from `bcsqrt()`: operand is probably negative");
        }
    }

    $periodPos = strpos($num, '.');

    // If there is no period in $num, scale is 0.
    if (false === $periodPos) {
        return 0;
    }

    // Count the number of digits to deduce what the current scale is.
    return strlen($num) - $periodPos - 1;
}
