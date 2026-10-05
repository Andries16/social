<?php declare(strict_types=1);

namespace Social\Domain\SocialGraph;

final class SocialGraphRepository
{
    public function __construct(private \PDO $db)
    {
    }

    public function userExists(int $userId): bool
    {
        $query = $this->db->prepare('SELECT id FROM users WHERE id=?');
        $query->execute([$userId]);

        return (bool) $query->fetchColumn();
    }

    public function isFollowing(int $followerId, int $followingId): bool
    {
        $query = $this->db->prepare(
            'SELECT 1 FROM follows WHERE follower_id=? AND following_id=?',
        );
        $query->execute([$followerId, $followingId]);

        return (bool) $query->fetchColumn();
    }

    public function follow(int $followerId, int $followingId): bool
    {
        $query = $this->db->prepare(
            'INSERT OR IGNORE INTO follows(follower_id,following_id,created_at)VALUES(?,?,?)',
        );
        $query->execute([$followerId, $followingId, date('c')]);

        return $query->rowCount() > 0;
    }

    public function unfollow(int $followerId, int $followingId): void
    {
        $query = $this->db->prepare(
            'DELETE FROM follows WHERE follower_id=? AND following_id=?',
        );
        $query->execute([$followerId, $followingId]);
    }

    public function list(int $userId, string $relation): array
    {
        $joinColumn = $relation === 'followers' ? 'follower_id' : 'following_id';
        $filterColumn = $relation === 'followers' ? 'following_id' : 'follower_id';

        $query = $this->db->prepare(
            "SELECT u.id,u.name,u.avatar
             FROM follows f
             JOIN users u ON u.id=f.{$joinColumn}
             WHERE f.{$filterColumn}=?
             ORDER BY u.name",
        );
        $query->execute([$userId]);

        return $query->fetchAll();
    }
}
