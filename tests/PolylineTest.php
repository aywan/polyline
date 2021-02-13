<?php

declare(strict_types=1);

namespace aywan\Polyline\tests;

use aywan\Polyline\Polyline;
use PHPUnit\Framework\TestCase;

class PolylineTest extends TestCase
{
    /**
     * @dataProvider encodeData
     *
     * @param array  $coordinates
     * @param string $expected
     */
    public function testEncode(array $coordinates, string $expected): void
    {
        self::assertEquals($expected, Polyline::encode($coordinates));
    }

    public function encodeData(): array
    {
        return [
            [
                [[38.5, -120.2], [40.7, -120.95], [43.252, -126.453]],
                '_p~iF~ps|U_ulLnnqC_mqNvxq`@',
            ],
        ];
    }

    /**
     * @dataProvider decodeData
     *
     * @param string $value
     * @param array  $expected
     */
    public function testDecode(string $value, array $expected): void
    {
        self::assertEquals($expected, Polyline::decode($value));
    }

    public function decodeData(): array
    {
        return [
            [
                '_p~iF~ps|U_ulLnnqC_mqNvxq`@',
                [[38.5, -120.2], [40.7, -120.95], [43.252, -126.453]],
            ],
        ];
    }
}
