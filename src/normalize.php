<?php

declare(strict_types=1);

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
