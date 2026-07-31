<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload and compress a single image
     * 
     * @param UploadedFile $image
     * @param string $path Directory path in storage
     * @param int $quality Compression quality (0-100)
     * @param int $maxWidth Maximum width of image
     * @return string|null Path to stored image or null if failed
     */
    public function uploadAndCompress(
        UploadedFile $image,
        string $path = 'products',
        int $quality = 75,
        int $maxWidth = 1920
    ): ?string {
        try {
            $realPath = $image->getRealPath();
            $mimeType = strtolower((string) $image->getMimeType());
            $extension = strtolower($image->getClientOriginalExtension());

            if ($this->isHeicFile($mimeType, $extension)) {
                return $this->handleHeicUpload($image, $path, $quality, $maxWidth);
            }

            $imageInfo = @getimagesize($realPath);
            if ($imageInfo === false) {
                if (Str::startsWith($mimeType, 'image/')) {
                    return $this->storeOriginalImage($image, $path, $extension ?: 'jpg');
                }

                throw new \Exception('Invalid image file');
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $imageType = $imageInfo[2];

            // Create temporary directory if it doesn't exist
            $tempDir = storage_path('temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            if (
                $image->getSize() <= (2 * 1024 * 1024)
                && $originalWidth <= $maxWidth
                && in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true)
            ) {
                return $this->storeOriginalImage($image, $path, $extension ?: 'jpg');
            }

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($realPath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($realPath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($realPath);
                    break;
                case IMAGETYPE_WEBP:
                    $sourceImage = imagecreatefromwebp($realPath);
                    break;
                default:
                    throw new \Exception('Unsupported image type');
            }

            if ($sourceImage === false) {
                throw new \Exception('Failed to load image');
            }

            // Calculate new dimensions
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;

            if ($originalWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int)($originalHeight * ($maxWidth / $originalWidth));
            }

            // Create a new image with the resized dimensions
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }

            // Resize the image
            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            // Save as compressed JPEG
            $filename = now()->timestamp . '_' . Str::random(10) . '.jpg';
            $tempPath = $tempDir . '/' . $filename;

            imagejpeg($resizedImage, $tempPath, $quality);

            // Clean up resources
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            // Store to public disk
            $storagePath = $path . '/' . $filename;
            Storage::disk('public')->put($storagePath, file_get_contents($tempPath));

            // Clean up temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return $storagePath;
        } catch (\Exception $e) {
            \Log::error('Image upload/compress failed: ' . $e->getMessage());
            return null;
        }
    }

    private function isHeicFile(string $mimeType, string $extension): bool
    {
        return in_array($extension, ['heic', 'heif'], true)
            || in_array($mimeType, ['image/heic', 'image/heif', 'image/heic-sequence', 'image/heif-sequence'], true);
    }

    private function handleHeicUpload(
        UploadedFile $image,
        string $path,
        int $quality,
        int $maxWidth
    ): ?string {
        $tempDir = storage_path('temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $convertedPath = $tempDir . '/' . now()->timestamp . '_' . Str::random(10) . '.jpg';

        try {
            if (class_exists(\Imagick::class)) {
                $imagick = new \Imagick();
                $imagick->readImage($image->getRealPath());
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompressionQuality($quality);

                if ($imagick->getImageWidth() > $maxWidth) {
                    $imagick->resizeImage($maxWidth, 0, \Imagick::FILTER_LANCZOS, 1);
                }

                $imagick->writeImage($convertedPath);
                $imagick->clear();
                $imagick->destroy();
            } else {
                $binary = trim((string) shell_exec('where magick 2>NUL'));
                if ($binary === '') {
                    $binary = trim((string) shell_exec('where convert 2>NUL'));
                }

                if ($binary !== '') {
                    @putenv('MAGICK_THREAD_LIMIT=1');
                    $source = str_replace('\\', '/', $image->getRealPath());
                    $target = str_replace('\\', '/', $convertedPath);
                    shell_exec("\"{$binary}\" \"{$source}\" -quality {$quality} -resize {$maxWidth}x\\> \"{$target}\" 2>&1");
                }
            }

            if (file_exists($convertedPath) && filesize($convertedPath) > 0) {
                $storedPath = $path . '/' . basename($convertedPath);
                Storage::disk('public')->put($storedPath, file_get_contents($convertedPath));
                @unlink($convertedPath);

                return $storedPath;
            }
        } catch (\Throwable $exception) {
            \Log::warning('HEIC conversion failed, storing original image instead.', [
                'file' => $image->getClientOriginalName(),
                'error' => $exception->getMessage(),
            ]);
        }

        if (file_exists($convertedPath)) {
            @unlink($convertedPath);
        }

        // Fallback: keep the original image so iPhone uploads do not fail completely.
        return $this->storeOriginalImage($image, $path, 'heic');
    }

    private function storeOriginalImage(UploadedFile $image, string $path, string $extension): ?string
    {
        $safeExtension = $extension ?: strtolower($image->getClientOriginalExtension() ?: 'jpg');
        $filename = now()->timestamp . '_' . Str::random(10) . '.' . $safeExtension;

        return $image->storeAs($path, $filename, 'public');
    }

    /**
     * Upload and compress multiple images
     * 
     * @param array $images Array of UploadedFile objects
     * @param string $path Directory path in storage
     * @param int $quality Compression quality (0-100)
     * @param int $maxWidth Maximum width of image
     * @return array Array of stored image paths
     */
    public function uploadAndCompressMultiple(
        array $images,
        string $path = 'products',
        int $quality = 75,
        int $maxWidth = 1920
    ): array {
        $imagePaths = [];

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $storagePath = $this->uploadAndCompress($image, $path, $quality, $maxWidth);
                if ($storagePath) {
                    $imagePaths[] = $storagePath;
                }
            }
        }

        return $imagePaths;
    }

    /**
     * Delete images from storage
     * 
     * @param array $imagePaths Array of image paths
     * @return bool
     */
    public function deleteImages(array $imagePaths): bool
    {
        try {
            foreach ($imagePaths as $imagePath) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}
