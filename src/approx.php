<?php

declare(strict_types=1);

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
