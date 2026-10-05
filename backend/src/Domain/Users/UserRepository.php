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

    public function search(string $term, int $viewerId, int $limit): array
    {
        $query = $this->db->prepare(
            'SELECT id,name,bio,avatar FROM users WHERE name LIKE ? ORDER BY name LIMIT ' . $limit,
        );
        $query->execute(['%' . $term . '%']);
        $users = $query->fetchAll();

        foreach ($users as &$candidate) {
            $settings = $this->db->prepare('SELECT private_account FROM user_settings WHERE user_id=?');
            $settings->execute([$candidate['id']]);
            $candidate['private_account'] = (bool) $settings->fetchColumn();

            if ($candidate['private_account'] && (int) $candidate['id'] !== $viewerId) {
                $follow = $this->db->prepare(
                    'SELECT 1 FROM follows WHERE follower_id=? AND following_id=?',
                );
                $follow->execute([$viewerId, (int) $candidate['id']]);

                if (!$follow->fetchColumn()) {
                    $candidate['bio'] = '';
                }
            }
        }

        return $users;
    }

    public function isFollowing(int $followerId, int $followingId): bool
    {
        $query = $this->db->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');
        $query->execute([$followerId, $followingId]);
        return (bool) $query->fetchColumn();
    }
}
