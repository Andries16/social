<?php declare(strict_types=1);

namespace Social\Domain\Users;

final class UserRepository
{
    public function __construct(private \PDO $db)
    {
    }

    public function findById(int $id): ?array
    {
        $query = $this->db->prepare('SELECT id,name,email,bio,avatar FROM users WHERE id=?');
        $query->execute([$id]);
        return $query->fetch() ?: null;
    }

    public function isPrivate(int $id): bool
    {
        $query = $this->db->prepare('SELECT private_account FROM user_settings WHERE user_id=?');
        $query->execute([$id]);
        return (bool) $query->fetchColumn();
    }

    public function isFollowing(int $followerId, int $followingId): bool
    {
        $query = $this->db->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');
        $query->execute([$followerId, $followingId]);
        return (bool) $query->fetchColumn();
    }
}
