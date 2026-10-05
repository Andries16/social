<?php declare(strict_types=1);

namespace Social\Domain\SocialGraph;

final class SocialGraphService
{
    public function __construct(private SocialGraphRepository $graph)
    {
    }

    public function follow(int $userId, int $targetId): bool
    {
        if ($targetId === $userId) {
            throw new \InvalidArgumentException('Cannot follow yourself');
        }

        if (!$this->graph->userExists($targetId)) {
            throw new \RuntimeException('User not found');
        }

        return $this->graph->follow($userId, $targetId);
    }

    public function unfollow(int $userId, int $targetId): void
    {
        if ($targetId === $userId) {
            throw new \InvalidArgumentException('Cannot follow yourself');
        }

        if (!$this->graph->userExists($targetId)) {
            throw new \RuntimeException('User not found');
        }

        $this->graph->unfollow($userId, $targetId);
    }

    public function list(int $userId, string $relation): array
    {
        if (!in_array($relation, ['followers', 'following'], true)) {
            throw new \InvalidArgumentException('Unsupported relationship');
        }

        return $this->graph->list($userId, $relation);
    }
}
