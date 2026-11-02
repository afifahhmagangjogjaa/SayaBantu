<?php

namespace App\Services;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
    /**
     * Compress, resize, and store an uploaded image to the public storage disk.
     *
     * @param  UploadedFile|mixed  $file
     * @param  string  $folder  Target directory inside public disk (e.g. 'helps', 'ktp-photos')
     * @param  int  $maxWidth  Maximum width in pixels (default 1280)
     * @param  int  $maxHeight  Maximum height in pixels (default 1280)
     * @param  int  $quality  JPEG/WebP quality percentage 1-100 (default 78)
     * @return string Stored file path relative to disk root (e.g. 'helps/abc123xyz.jpg')
     */
    public static function compressAndStore(
        $file,
        string $folder,
        int $maxWidth = 1280,
        int $maxHeight = 1280,
        int $quality = 78
    ): string {
        // Fallback jika bukan UploadedFile atau file tidak valid
        if (! $file || ! ($file instanceof UploadedFile) || ! $file->isValid()) {
            if ($file && method_exists($file, 'store')) {
                return $file->store($folder, 'public');
            }

            throw new \InvalidArgumentException('File upload tidak valid.');
        }

        $realPath = $file->getRealPath();
        if (! $realPath || ! file_exists($realPath)) {
            return $file->store($folder, 'public');
        }

        // Dapatkan informasi ukuran & tipe MIME gambar
        $imageInfo = @getimagesize($realPath);
        if ($imageInfo === false) {
            // Bukan format gambar yang didukung getimagesize (misal SVG / dokumen), simpan normal
            return $file->store($folder, 'public');
        }

        $origWidth = $imageInfo[0];
        $origHeight = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Jika GD extension tidak aktif, fallback ke penyimpanan standar Laravel
        if (! extension_loaded('gd')) {
            return $file->store($folder, 'public');
        }

        try {
            // Muat gambar ke resource GD berdasarkan MIME
            $sourceImage = null;
            $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');

            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/pjpeg':
                    $sourceImage = @imagecreatefromjpeg($realPath);
                    $extension = 'jpg';
                    break;
                case 'image/png':
                case 'image/x-png':
                    $sourceImage = @imagecreatefrompng($realPath);
                    $extension = 'png';
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = @imagecreatefromwebp($realPath);
                        $extension = 'webp';
                    }
                    break;
                case 'image/gif':
                    // GIF biasanya animasi atau icon, simpan as-is jika tidak ingin merusak animasi
                    return $file->store($folder, 'public');
            }

            if (! $sourceImage) {
                return $file->store($folder, 'public');
            }

            // Tangani orientasi EXIF untuk kamera HP (terutama JPEG)
            if (($mime === 'image/jpeg' || $mime === 'image/jpg') && function_exists('exif_read_data')) {
                try {
                    $exif = @exif_read_data($realPath);
                    if (! empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $sourceImage = imagerotate($sourceImage, 180, 0);
                                break;
                            case 6:
                                $sourceImage = imagerotate($sourceImage, -90, 0);
                                $tempW = $origWidth;
                                $origWidth = $origHeight;
                                $origHeight = $tempW;
                                break;
                            case 8:
                                $sourceImage = imagerotate($sourceImage, 90, 0);
                                $tempW = $origWidth;
                                $origWidth = $origHeight;
                                $origHeight = $tempW;
                                break;
                        }
                    }
                } catch (\Throwable $ex) {
                    // Abaikan error pembacaan EXIF jika metadata rusak
                }
            }

            // Hitung skala resize proporsional
            $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1.0);
            $newWidth = (int) max(1, round($origWidth * $ratio));
            $newHeight = (int) max(1, round($origHeight * $ratio));

            // Buat canvas baru dengan resolusi target
            $targetImage = imagecreatetruecolor($newWidth, $newHeight);

            // Pertahankan transparansi untuk PNG
            if ($extension === 'png') {
                imagealphablending($targetImage, false);
                imagesavealpha($targetImage, true);
                $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
                imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resample gambar
            imagecopyresampled(
                $targetImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $origWidth,
                $origHeight
            );

            // Buat file temporary lokal untuk hasil kompresi
            $tempFile = tempnam(sys_get_temp_dir(), 'sb_comp_').'.'.$extension;

            if ($extension === 'png') {
                // Konversi quality 1-100 ke kompresi level PNG 0-9
                $pngCompression = (int) round((100 - $quality) / 10);
                $pngCompression = max(0, min(9, $pngCompression));
                imagepng($targetImage, $tempFile, $pngCompression);
            } elseif ($extension === 'webp' && function_exists('imagewebp')) {
                imagewebp($targetImage, $tempFile, $quality);
            } else {
                imagejpeg($targetImage, $tempFile, $quality);
                $extension = 'jpg';
            }

            // Bebaskan memori GD
            imagedestroy($sourceImage);
            imagedestroy($targetImage);

            // Generate nama file acak yang unik dan aman
            $filename = Str::random(40).'.'.$extension;
            $relativePath = trim($folder, '/').'/'.$filename;

            // Simpan ke storage disk public
            Storage::disk('public')->putFileAs($folder, new File($tempFile), $filename);

            // Hapus file temporary
            @unlink($tempFile);

            return $relativePath;
        } catch (\Throwable $e) {
            Log::warning('ImageCompressionService failed, fallback to standard store: '.$e->getMessage());

            return $file->store($folder, 'public');
        }
    }
}
