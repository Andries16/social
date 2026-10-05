<?php declare(strict_types=1);

namespace Social\Domain\Media;

final class MediaService
{
    public function __construct(private MediaRepository $media)
    {
    }

    public function upload(array $file): string
    {
        return $this->media->store($file);
    }
}
