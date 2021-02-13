<?php

declare(strict_types=1);

namespace aywan\Polyline;

class Polyline
{
    /**
     * Decoding string into list of [Latitude, Longitude] pairs.
     *
     * @param string $value
     * @param int    $precision
     *
     * @return array<array<float>>
     */
    public static function decode(string $value, int $precision = 5): array
    {
        $coordinates = [];

        $factor = 10 ** $precision;
        $index = 0;
        $lat = 0;
        $lng = 0;
        $strLen = strlen($value);
        while ($index < $strLen) {
            $lat += self::decodeNextCoordinate($value, $index);
            $lng += self::decodeNextCoordinate($value, $index);

            $coordinates[] = [
                $lat / $factor,
                $lng / $factor,
            ];
        }

        return $coordinates;
    }

    /**
     * @param string $value
     * @param int    $index
     *
     * @return mixed
     */
    private static function decodeNextCoordinate(string &$value, int &$index): int
    {
        $shift = 0;
        $result = 0;

        do {
            $byte = ord($value[$index++]) - 63;
            $result |= ($byte & 0x1f) << $shift;
            $shift += 5;
        } while ($byte >= 0x20);

        return ($result & 1) ? ~($result >> 1) : $result >> 1;
    }

    /**
     * Encoding list of [Latitude, Longitude] pairs into string.
     *
     * @param array<array<float>> $coordinates
     * @param int                 $precision
     *
     * @return string
     */
    public static function encode(array $coordinates, int $precision = 5): string
    {
        if (empty($coordinates)) {
            return '';
        }

        $factor = 10 ** $precision;
        $output = '';

        $prevLat = 0;
        $prevLng = 0;
        foreach ($coordinates as $c) {
            $curLat = static::round($c[0] * $factor);
            $curLng = static::round($c[1] * $factor);

            $output .= static::encodeCoordinate($curLat, $prevLat) . static::encodeCoordinate($curLng, $prevLng);

            $prevLat = $curLat;
            $prevLng = $curLng;
        }

        return $output;
    }

    private static function round(float $value): int
    {
        return (int)floor(abs($value + 0.5) * ($value >= 0 ? 1 : -1));
    }

    private static function encodeCoordinate(int $current, int $previous): string
    {
        $coordinate = ($current - $previous) << 1;
        if ($current < $previous) {
            $coordinate = ~$coordinate;
        }

        $output = '';
        while ($coordinate >= 0x20) {
            $output .= chr((0x20 | ($coordinate & 0x1f)) + 63);
            $coordinate >>= 5;
        }
        $output .= chr($coordinate + 63);

        return $output;
    }
}