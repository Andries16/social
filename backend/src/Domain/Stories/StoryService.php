<?php declare(strict_types=1);

namespace Social\Domain\Stories;

final class StoryService
{
    public function __construct(private StoryRepository $stories)
    {
    }

    public function list(int $viewerId): array
    {
        return ['stories' => $this->stories->listVisible($viewerId)];
    }

    public function create(int $userId, array $payload): void
    {
        $text = trim((string) ($payload['text'] ?? ''));
        if ($text === '' || mb_strlen($text) > 1000) {
            throw new \InvalidArgumentException('Story text must contain between 1 and 1000 characters');
        }

        $this->stories->create($userId, $text);
    }

    public function view(int $viewerId, int $storyId): void
    {
        if (!$this->stories->canView($storyId, $viewerId)) {
            throw new \RuntimeException('Story not found');
        }

        $this->stories->markViewed($storyId, $viewerId);
    }
}
