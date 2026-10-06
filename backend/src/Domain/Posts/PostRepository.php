<?php declare(strict_types=1);

namespace Social\Domain\Posts;

final class PostRepository
{
    public function __construct(private \PDO $db)
    {
    }

    public function findOwnerId(int $postId): ?int
    {
        $query = $this->db->prepare('SELECT user_id FROM posts WHERE id=?');
        $query->execute([$postId]);
        $value = $query->fetchColumn();

        return $value === false ? null : (int) $value;
    }

    public function canView(int $postId, int $viewerId): bool
    {
        $query = $this->db->prepare(
            'SELECT p.user_id,
                    COALESCE((SELECT private_account FROM user_settings s WHERE s.user_id=p.user_id),0)
                        private_account
             FROM posts p
             WHERE p.id=?',
        );
        $query->execute([$postId]);
        $post = $query->fetch();

        if ($post === false) {
            return false;
        }

        if (!(bool) $post['private_account'] || (int) $post['user_id'] === $viewerId) {
            return true;
        }

        $follow = $this->db->prepare(
            'SELECT 1 FROM follows WHERE follower_id=? AND following_id=?',
        );
        $follow->execute([$viewerId, (int) $post['user_id']]);

        return (bool) $follow->fetchColumn();
    }

    public function listForFeed(int $viewerId, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $query = $this->db->prepare(
            "SELECT p.*,u.id uid,u.name,u.bio,u.avatar,
                    (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id) likes,
                    (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id AND r.reaction='like') reaction_like,
                    (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id AND r.reaction='love') reaction_love,
                    (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id AND r.reaction='laugh') reaction_laugh,
                    (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id AND r.reaction='wow') reaction_wow,
                    (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id AND r.reaction='sad') reaction_sad,
                    (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id AND r.reaction='angry') reaction_angry
             FROM posts p
             JOIN users u ON u.id=p.user_id
             WHERE COALESCE((SELECT private_account FROM user_settings s WHERE s.user_id=p.user_id),0)=0
                OR p.user_id=?
                OR EXISTS(
                    SELECT 1 FROM follows f
                    WHERE f.follower_id=? AND f.following_id=p.user_id
                )
             ORDER BY p.id DESC
             LIMIT ? OFFSET ?",
        );
        $query->bindValue(1, $viewerId, \PDO::PARAM_INT);
        $query->bindValue(2, $viewerId, \PDO::PARAM_INT);
        $query->bindValue(3, $limit + 1, \PDO::PARAM_INT);
        $query->bindValue(4, $offset, \PDO::PARAM_INT);
        $query->execute();

        $posts = $query->fetchAll();

        return [
            'posts' => $posts,
            'hasMore' => count($posts) > $limit,
        ];
    }

    public function comments(int $postId): array
    {
        $query = $this->db->prepare(
            'SELECT c.*,u.name,u.avatar
             FROM comments c
             JOIN users u ON u.id=c.user_id
             WHERE c.post_id=?
             ORDER BY c.id',
        );
        $query->execute([$postId]);
        $comments = $query->fetchAll();

        foreach ($comments as &$comment) {
            $comment['user'] = [
                'id' => $comment['user_id'],
                'name' => $comment['name'],
                'avatar' => $comment['avatar'],
            ];
        }

        return $comments;
    }

    public function create(int $userId, string $body, string $image, array $media = []): int
    {
        $query = $this->db->prepare(
            'INSERT INTO posts(user_id,body,image,created_at)VALUES(?,?,?,?)',
        );
        $query->execute([$userId, $body, $image, date('c')]);
        $postId = (int) $this->db->lastInsertId();
        $mediaQuery = $this->db->prepare('INSERT INTO post_media(post_id,url,media_type,position)VALUES(?,?,?,?)');
        foreach (array_values($media) as $position => $url) {
            $mediaQuery->execute([$postId, $url, 'image', $position]);
        }
        return $postId;
    }

    public function media(int $postId): array
    {
        $query = $this->db->prepare('SELECT url,media_type FROM post_media WHERE post_id=? ORDER BY position,id');
        $query->execute([$postId]);
        return $query->fetchAll();
    }

    public function replaceMedia(int $postId, array $media): void
    {
        $delete = $this->db->prepare('DELETE FROM post_media WHERE post_id=?');
        $delete->execute([$postId]);
        $insert = $this->db->prepare('INSERT INTO post_media(post_id,url,media_type,position)VALUES(?,?,?,?)');
        foreach (array_values($media) as $position => $url) {
            $insert->execute([$postId, $url, 'image', $position]);
        }
    }

    public function update(int $postId, string $body, string $image): void
    {
        $query = $this->db->prepare('UPDATE posts SET body=?,image=? WHERE id=?');
        $query->execute([$body, $image, $postId]);
    }

    public function delete(int $postId): void
    {
        $this->db->beginTransaction();

        try {
            foreach (['reactions', 'comments', 'notifications'] as $table) {
                $query = $this->db->prepare("DELETE FROM {$table} WHERE post_id=?");
                $query->execute([$postId]);
            }

            $query = $this->db->prepare('DELETE FROM posts WHERE id=?');
            $query->execute([$postId]);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function react(int $postId, int $userId, string $reaction): void
    {
        $query = $this->db->prepare(
            'INSERT INTO reactions(post_id,user_id,reaction)
             VALUES(?,?,?)
             ON CONFLICT(post_id,user_id)
             DO UPDATE SET reaction=excluded.reaction',
        );
        $query->execute([$postId, $userId, $reaction]);
    }

    public function createComment(int $postId, int $userId, string $text): void
    {
        $query = $this->db->prepare(
            'INSERT INTO comments(post_id,user_id,text,created_at)VALUES(?,?,?,?)',
        );
        $query->execute([$postId, $userId, $text, date('c')]);
    }

    public function findCommentOwnerId(int $commentId): ?int
    {
        $query = $this->db->prepare('SELECT user_id FROM comments WHERE id=?');
        $query->execute([$commentId]);
        $value = $query->fetchColumn();

        return $value === false ? null : (int) $value;
    }

    public function updateComment(int $commentId, string $text): void
    {
        $query = $this->db->prepare('UPDATE comments SET text=? WHERE id=?');
        $query->execute([$text, $commentId]);
    }

    public function deleteComment(int $commentId): void
    {
        $query = $this->db->prepare('DELETE FROM comments WHERE id=?');
        $query->execute([$commentId]);
    }
}
