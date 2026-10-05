<?php declare(strict_types=1);

namespace Social\Domain\Media;

final class MediaRepository
{
    public function __construct(private string $directory)
    {
    }

    public function store(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Upload failed');
        }

        if (($file['size'] ?? 0) > 5242880) {
            throw new \InvalidArgumentException('Maximum file size is 5 MB');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string) $file['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        if (!isset($allowed[$mime])) {
            throw new \InvalidArgumentException('Unsupported image type');
        }

        if (!is_dir($this->directory) && !mkdir($this->directory, 0755, true)) {
            throw new \RuntimeException('Upload storage unavailable');
        }

        $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], $this->directory . '/' . $name)) {
            throw new \RuntimeException('Unable to store upload');
        }

        return $name;
    }
}
