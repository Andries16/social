<?php declare(strict_types=1);

namespace Social\Domain\Media;

final class MediaRepository
{
    public function __construct(private \PDO $db, private string $directory)
    {
    }

    public function store(int $userId, array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Upload failed');
        }
        if (($file['size'] ?? 0) > 5242880) {
            throw new \InvalidArgumentException('Maximum file size is 5 MB');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string) $file['tmp_name']);
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
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

        try {
            $query = $this->db->prepare('INSERT INTO media(user_id,storage_name,mime_type,created_at)VALUES(?,?,?,?)');
            $query->execute([$userId, $name, $mime, date('c')]);
        } catch (\Throwable $e) {
            @unlink($this->directory . '/' . $name);
            throw $e;
        }

        return $name;
    }

    public function ownedStorageName(int $userId, string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!is_string($path) || !str_starts_with($path, '/uploads/')) {
            return null;
        }
        $name = basename($path);
        if ($name === '' || $name === '.' || $name === '..' || !preg_match('/^[a-f0-9]{32}\\.(?:jpg|png|webp|gif)$/', $name)) {
            return null;
        }

        $query = $this->db->prepare('SELECT storage_name FROM media WHERE storage_name=? AND user_id=?');
        $query->execute([$name, $userId]);
        $owned = $query->fetchColumn();
        return $owned === false ? null : (string) $owned;
    }
}
