<?php

declare(strict_types=1);

require_once 'approx.php';
require_once 'mul.php';
require_once 'normalize.php';
require_once 'raise.php';
require_once 'root.php';
require_once 'round.php';
require_once 'signature.php';

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
