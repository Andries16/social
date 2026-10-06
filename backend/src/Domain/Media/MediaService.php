<?php declare(strict_types=1);

namespace Social\Domain\Media;

final class MediaService
{
    public function __construct(private MediaRepository $media)
    {
    }

    public function upload(int $userId, array $file): string
    {
        return $this->media->store($userId, $file);
    }

    public function ownedUrl(int $userId, string $url): ?string
    {
        $name = $this->media->ownedStorageName($userId, $url);
        return $name === null ? null : '/uploads/' . rawurlencode($name);
    }
}
