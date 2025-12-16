<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Helper class untuk operasi media (file type detection).
 * 
 * Menggunakan extension-based detection untuk performa optimal.
 */
class MediaHelper
{
    /**
     * Mapping dari main MIME type ke Telegram media type.
     */
    protected const TYPE_MAPPING = [
        'image' => 'photo',
        'video' => 'video',
        'audio' => 'audio',
        'application' => 'document',
        'text' => 'document',
    ];

    /**
     * Extension to MIME type mapping.
     */
    protected const EXTENSION_MAPPING = [
        // Images
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'bmp' => 'image/bmp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        
        // Videos
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'mkv' => 'video/x-matroska',
        'webm' => 'video/webm',
        'flv' => 'video/x-flv',
        'wmv' => 'video/x-ms-wmv',
        '3gp' => 'video/3gpp',
        
        // Audio
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'flac' => 'audio/flac',
        'm4a' => 'audio/mp4',
        'aac' => 'audio/aac',
        'wma' => 'audio/x-ms-wma',
        
        // Documents
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        '7z' => 'application/x-7z-compressed',
        'tar' => 'application/x-tar',
        'gz' => 'application/gzip',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'html' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
    ];

    /**
     * Extension to Telegram media type (direct mapping for speed).
     */
    protected const EXTENSION_TO_TYPE = [
        // Photos
        'jpg' => 'photo', 'jpeg' => 'photo', 'png' => 'photo', 
        'gif' => 'photo', 'webp' => 'photo', 'bmp' => 'photo',
        
        // Videos
        'mp4' => 'video', 'avi' => 'video', 'mov' => 'video',
        'mkv' => 'video', 'webm' => 'video', 'flv' => 'video',
        'wmv' => 'video', '3gp' => 'video',
        
        // Audio
        'mp3' => 'audio', 'wav' => 'audio', 'ogg' => 'audio',
        'flac' => 'audio', 'm4a' => 'audio', 'aac' => 'audio',
        'wma' => 'audio',
        
        // Voice (special Telegram type)
        'oga' => 'voice',
    ];

    /**
     * Get Telegram media type dari file URL/path.
     * 
     * @param string $fileUrl URL atau path file
     * @return string Telegram media type (photo, video, audio, voice, document)
     */
    public static function getMediaType(string $fileUrl): string
    {
        $extension = self::getExtension($fileUrl);
        return self::EXTENSION_TO_TYPE[$extension] ?? 'document';
    }

    /**
     * Get MIME type dari file extension.
     * 
     * @param string $fileUrl URL atau path file
     * @return string MIME type
     */
    public static function getMimeType(string $fileUrl): string
    {
        $extension = self::getExtension($fileUrl);
        return self::EXTENSION_MAPPING[$extension] ?? 'application/octet-stream';
    }

    /**
     * Get file extension dari URL/path.
     * 
     * @param string $fileUrl URL atau path file
     * @return string Extension (lowercase, tanpa dot)
     */
    public static function getExtension(string $fileUrl): string
    {
        // Parse URL untuk mendapatkan path (remove query string)
        $parsedUrl = parse_url($fileUrl);
        $path = $parsedUrl['path'] ?? $fileUrl;
        
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Check apakah file adalah image.
     */
    public static function isImage(string $fileUrl): bool
    {
        return self::getMediaType($fileUrl) === 'photo';
    }

    /**
     * Check apakah file adalah video.
     */
    public static function isVideo(string $fileUrl): bool
    {
        return self::getMediaType($fileUrl) === 'video';
    }

    /**
     * Check apakah file adalah audio.
     */
    public static function isAudio(string $fileUrl): bool
    {
        return self::getMediaType($fileUrl) === 'audio';
    }

    /**
     * Check apakah file adalah document.
     */
    public static function isDocument(string $fileUrl): bool
    {
        return self::getMediaType($fileUrl) === 'document';
    }

    /**
     * Get all supported image extensions.
     */
    public static function getImageExtensions(): array
    {
        return array_keys(array_filter(self::EXTENSION_TO_TYPE, fn($type) => $type === 'photo'));
    }

    /**
     * Get all supported video extensions.
     */
    public static function getVideoExtensions(): array
    {
        return array_keys(array_filter(self::EXTENSION_TO_TYPE, fn($type) => $type === 'video'));
    }

    /**
     * Get all supported audio extensions.
     */
    public static function getAudioExtensions(): array
    {
        return array_keys(array_filter(self::EXTENSION_TO_TYPE, fn($type) => $type === 'audio'));
    }
}
