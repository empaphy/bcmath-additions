<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function empaphy\bcmaph\bcroot;

final class RootTest extends TestCase
{
    /**
     * @dataProvider bcrootDataProvider
     *
     * @param  numeric-string  $num
     * @param  numeric-string  $n
     * @param  int             $scale
     * @param  numeric-string  $expected
     * @return void
     */
    public function test_bcroot(string $num, string $n, int $scale, string $expected)
    {
        $this->assertEquals($expected, bcroot($num, $n, $scale));
    }

    /**
     * @return array<int, array{0: numeric-string, 1: numeric-string, 2: int, 3: numeric-string}>
     */
    public function bcrootDataProvider(): array
    {
        return [
            [
                '17',
                '19',
                15,
                '1.160808205961694',
            ],
            [
                '7',
                '24',
                46,
                '1.0844572045864763396830684847861137767791284855',
            ],
            [
                '1',
                '7',
                7,
                '1.0000000',
            ],
            [
                '13',
                '1',
                5,
                '13.00000',
            ],
            [
                '7',
                '24',
                129,
                '1.084457204586476339683068484786113776779128485499972439179533144286867274051399621715070401611661590895980090316271185372465974063',
            ],
        ];
    }
}