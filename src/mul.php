<?php

declare(strict_types=1);

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
