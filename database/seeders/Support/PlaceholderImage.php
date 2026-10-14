<?php

declare(strict_types=1);

namespace Database\Seeders\Support;

/**
 * Generates simple local placeholder images (no external hotlinking) for seed data.
 */
final class PlaceholderImage
{
    /**
     * @param  array{0:int,1:int,2:int}  $rgb
     */
    public static function make(string $label, array $rgb = [15, 76, 129], int $width = 1600, int $height = 1000, bool $car = false): string
    {
        $img = imagecreatetruecolor($width, $height);

        // Vertical gradient background.
        for ($y = 0; $y < $height; $y++) {
            $t = $y / $height;
            $c = imagecolorallocate(
                $img,
                (int) min(255, $rgb[0] + 70 * $t),
                (int) min(255, $rgb[1] + 70 * $t),
                (int) min(255, $rgb[2] + 70 * $t),
            );
            imageline($img, 0, $y, $width, $y, $c);
        }

        $white = imagecolorallocate($img, 255, 255, 255);
        $shade = imagecolorallocatealpha($img, 0, 0, 0, 80);

        if ($car) {
            self::drawCar($img, $width, $height, $white, $shade);
        }

        // Label using the largest built-in font, scaled up via an intermediate image.
        $font = 5;
        $tw = imagefontwidth($font) * strlen($label);
        $th = imagefontheight($font);
        $text = imagecreatetruecolor(max(1, $tw), $th);
        imagesavealpha($text, true);
        imagefill($text, 0, 0, imagecolorallocatealpha($text, 0, 0, 0, 127));
        imagestring($text, $font, 0, 0, $label, imagecolorallocate($text, 255, 255, 255));
        $scale = max(1, (int) floor(min(($width * 0.8) / max(1, $tw), ($height * 0.12) / $th)));
        $dw = $tw * $scale;
        $dh = $th * $scale;
        imagecopyresized($img, $text, (int) (($width - $dw) / 2), (int) ($car ? $height * 0.12 : ($height - $dh) / 2), 0, 0, $dw, $dh, $tw, $th);
        imagedestroy($text);

        $path = tempnam(sys_get_temp_dir(), 'ph').'.jpg';
        imagejpeg($img, $path, 85);
        imagedestroy($img);

        return $path;
    }

    private static function drawCar(\GdImage $img, int $w, int $h, int $body, int $shade): void
    {
        $cx = $w / 2;
        $base = (int) ($h * 0.72);
        $len = (int) ($w * 0.62);
        $x0 = (int) ($cx - $len / 2);

        imagefilledellipse($img, (int) $cx, $base + (int) ($h * 0.06), $len + 80, (int) ($h * 0.06), $shade);
        // Body and cabin.
        imagefilledrectangle($img, $x0, $base - (int) ($h * 0.16), $x0 + $len, $base, $body);
        imagefilledpolygon($img, [
            $x0 + (int) ($len * 0.2), $base - (int) ($h * 0.16),
            $x0 + (int) ($len * 0.32), $base - (int) ($h * 0.3),
            $x0 + (int) ($len * 0.68), $base - (int) ($h * 0.3),
            $x0 + (int) ($len * 0.82), $base - (int) ($h * 0.16),
        ], $body);
        // Wheels.
        $wheel = imagecolorallocate($img, 30, 30, 35);
        foreach ([0.2, 0.8] as $p) {
            imagefilledellipse($img, $x0 + (int) ($len * $p), $base, (int) ($h * 0.16), (int) ($h * 0.16), $wheel);
        }
    }
}
