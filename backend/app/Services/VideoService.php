<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService
{
    /**
     * Generate pre-signed S3 upload URL for video
     */
    public function generateUploadUrl(string $filename, int $fileSize, string $contentType = 'video/mp4'): array
    {
        // For local development, use local storage
        // For production, this would generate S3 pre-signed POST URL
        $disk = Storage::disk(config('filesystems.default'));

        $path = 'videos/' . Str::uuid() . '/' . $filename;

        // In production with S3, use:
        // $client = Storage::disk('s3')->getClient();
        // $command = $client->getCommand('PutObject', [
        //     'Bucket' => config('filesystems.disks.s3.bucket'),
        //     'Key' => $path,
        //     'ContentType' => $contentType,
        // ]);
        // $presignedRequest = $client->createPresignedRequest($command, '+1 hour');

        return [
            'upload_url' => $disk->url($path),
            'path' => $path,
            'expires_at' => now()->addHours(1)->toISOString(),
        ];
    }

    /**
     * T139: Generate signed CloudFront URL for authenticated video access
     */
    public function generatePlaybackUrl(string $videoPath, int $expirationHours = 24): string
    {
        // For local development, return local storage URL
        if (config('filesystems.default') === 'local') {
            return Storage::disk('public')->url($videoPath);
        }

        // For production with CloudFront, generate signed URL
        // T139: This would use AWS CloudFront signed URLs with private key
        // Example implementation:
        // $cloudFrontUrl = config('services.cloudfront.url') . '/' . $videoPath;
        // $expires = time() + ($expirationHours * 3600);
        // $signer = new CloudFrontUrlSigner(
        //     config('services.cloudfront.key_pair_id'),
        //     config('services.cloudfront.private_key_path')
        // );
        // return $signer->getSignedUrl($cloudFrontUrl, $expires);
        
        // For now, return S3 URL (in production, use CloudFront distribution URL)
        return Storage::disk('s3')->url($videoPath);
    }

    /**
     * Store uploaded video file
     */
    public function storeVideo(UploadedFile $file, string $courseId, string $lessonId): string
    {
        $path = "courses/{$courseId}/lessons/{$lessonId}/{$file->getClientOriginalName()}";
        
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    /**
     * Delete video file
     */
    public function deleteVideo(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Get video file size
     */
    public function getVideoSize(string $path): int
    {
        return Storage::disk('public')->size($path);
    }
}

