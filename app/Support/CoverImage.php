<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores publication covers scaled down and re-encoded, the same way the Nexus app does with
 * post photos: a 12 MP phone picture becomes a ~300 KB file that still looks sharp on a
 * retina screen, so the blog loads faster and the disk fills slower.
 */
class CoverImage
{
    /** Longest side, in pixels, of a stored cover. */
    public const MAX_SIDE = 1600;

    private const JPEG_QUALITY = 82;

    private const WEBP_QUALITY = 80;

    /** Above this many pixels (~50 MP) the file is stored as uploaded. */
    private const MAX_PIXELS = 50_000_000;

    /**
     * Saves the upload under "publications/" on the public disk and returns its path.
     * Anything GD cannot handle (no GD, GIF animations, broken files) is stored as uploaded.
     */
    public static function store(UploadedFile $file): string
    {
        $optimized = self::optimize($file);

        if ($optimized === null) {
            return $file->store('publications', 'public');
        }

        [$bytes, $extension] = $optimized;
        $path = 'publications/'.Str::random(40).'.'.$extension;
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }

    /**
     * @return array{0: string, 1: string}|null the encoded bytes and their extension
     */
    private static function optimize(UploadedFile $file): ?array
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $info = @getimagesize($file->getRealPath());
        if ($info === false) {
            return null;
        }

        [$width, $height, $type] = $info;

        // GD holds ~5 bytes per pixel decoded: a 12 MP photo needs ~60 MB. Make room for it,
        // and leave absurdly large images untouched instead of running out of memory
        if ($width * $height > self::MAX_PIXELS || ! self::ensureMemoryFor($width * $height)) {
            return null;
        }

        // GIFs may be animated; re-encoding would keep only the first frame
        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => @imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : false,
            default => false,
        };
        if ($image === false) {
            return null;
        }

        if ($type === IMAGETYPE_JPEG) {
            $image = self::applyExifOrientation($image, $file->getRealPath());
            $width = imagesx($image);
            $height = imagesy($image);
        }

        $scale = min(1, self::MAX_SIDE / max($width, $height));
        if ($scale < 1) {
            // imagecopyresampled only allocates the destination, unlike imagescale()
            $newWidth = max(1, (int) round($width * $scale));
            $newHeight = max(1, (int) round($height * $scale));
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            if ($resized !== false) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }
        }

        // PNG and WebP may be transparent: WebP keeps the alpha channel and is still small
        $keepAlpha = $type !== IMAGETYPE_JPEG && function_exists('imagewebp');
        ob_start();
        if ($keepAlpha) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $ok = imagewebp($image, null, self::WEBP_QUALITY);
        } else {
            imageinterlace($image, true);
            $ok = imagejpeg($image, null, self::JPEG_QUALITY);
        }
        $bytes = ob_get_clean();
        imagedestroy($image);

        if (! $ok || $bytes === false || $bytes === '') {
            return null;
        }

        // Re-encoding a small, already compressed file can make it bigger: keep the original then
        if ($scale >= 1 && strlen($bytes) >= $file->getSize()) {
            return null;
        }

        return [$bytes, $keepAlpha ? 'webp' : 'jpg'];
    }

    /**
     * Raises the memory limit when decoding needs more than what is left; false if it cannot.
     */
    private static function ensureMemoryFor(int $pixels): bool
    {
        $limit = self::bytes((string) ini_get('memory_limit'));
        if ($limit < 0) {
            return true;
        }

        // Source + rotated or scaled copy + encoder buffers
        $needed = memory_get_usage() + (int) ($pixels * 5 * 2.2) + 16 * 1024 * 1024;
        if ($needed <= $limit) {
            return true;
        }

        return ini_set('memory_limit', (string) $needed) !== false;
    }

    private static function bytes(string $value): int
    {
        $value = trim($value);
        if ($value === '' || $value === '-1') {
            return -1;
        }

        $number = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }

    /**
     * Phones store photos sideways with an EXIF flag; GD ignores it, so the turn is applied here.
     */
    private static function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };
        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if ($rotated === false) {
            return $image;
        }
        imagedestroy($image);

        return $rotated;
    }
}
