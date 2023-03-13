<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RootTest extends TestCase
{
    /**
     * @dataProvider bcrootDataProvider
     *
     * @param  string    $expected
     * @param  string    $num
     * @param  string    $n
     * @param  int|null  $scale
     * @return void
     */
    public function testBcroot(string $expected, string $num, string $n, ?int $scale): void
    {
        $actual = bcroot($num, $n, $scale);
        $this->assertEquals($expected, $actual);
    }

    public function bcrootDataProvider(): array
    {
        return [
            [
                '1.160808205961694',
                '17',
                '19',
                15,
            ],
            [
                '1.0844572045864763396830684847861137767791284855',
                '7',
                '24',
                46,
            ],
            [
                '1.0000000',
                '1',
                '7',
                7,
            ],
            [
                '13.00000',
                '13',
                '1',
                5,
            ],
            [
                '1.084457204586476339683068484786113776779128485499972439179533144286867274051399621715070401611661590895980090316271185372465974063',
                '7',
                '24',
                129,
            ],
        ];
    }
}