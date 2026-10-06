<?php declare(strict_types=1);

namespace Social\Domain\Stories;

final class StoryRepository
{
    public function __construct(private \PDO $db)
    {
    }

    public function listVisible(int $viewerId): array
    {
        $query = $this->db->prepare(
            "SELECT s.*, u.id uid, u.name, u.avatar
             FROM stories s
             JOIN users u ON u.id=s.user_id
             WHERE datetime(s.created_at) >= datetime('now','-1 day')
               AND (
                   COALESCE((SELECT private_account FROM user_settings s2 WHERE s2.user_id=s.user_id),0)=0
                   OR s.user_id=?
                   OR EXISTS(
                       SELECT 1 FROM follows f
                       WHERE f.follower_id=? AND f.following_id=s.user_id
                   )
               )
             ORDER BY s.id DESC",
        );
        $query->execute([$viewerId, $viewerId]);
        $stories = $query->fetchAll();

        foreach ($stories as &$story) {
            $story['user'] = [
                'id' => $story['uid'],
                'name' => $story['name'],
                'avatar' => $story['avatar'],
            ];
        }

        return $stories;
    }

    public function create(int $userId, string $text): int
    {
        $query = $this->db->prepare(
            'INSERT INTO stories(user_id,text,created_at) VALUES(?,?,?)',
        );
        $query->execute([$userId, $text, date('c')]);

        return (int) $this->db->lastInsertId();
    }

    public function canView(int $storyId, int $viewerId): bool
    {
        $query = $this->db->prepare(
            "SELECT s.user_id,
                    COALESCE((SELECT private_account FROM user_settings WHERE user_id=s.user_id),0)
                        private_account
             FROM stories s
             WHERE s.id=? AND datetime(s.created_at) >= datetime('now','-1 day')",
        );
        $query->execute([$storyId]);
        $story = $query->fetch();

        if ($story === false) {
            return false;
        }

        if (!(bool) $story['private_account'] || (int) $story['user_id'] === $viewerId) {
            return true;
        }

        $follow = $this->db->prepare(
            'SELECT 1 FROM follows WHERE follower_id=? AND following_id=?',
        );
        $follow->execute([$viewerId, (int) $story['user_id']]);

        return (bool) $follow->fetchColumn();
    }

    public function markViewed(int $storyId, int $userId): void
    {
        $query = $this->db->prepare(
            'INSERT INTO story_views(story_id,user_id,created_at)
             VALUES(?,?,?)
             ON CONFLICT(story_id,user_id) DO NOTHING',
        );
        $query->execute([$storyId, $userId]);
    }
}
