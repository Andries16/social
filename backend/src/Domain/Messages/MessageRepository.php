<?php declare(strict_types=1);

namespace Social\Domain\Messages;

final class MessageRepository
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

    public function conversation(int $userId, int $participantId): array
    {
        $query = $this->db->prepare(
            'SELECT * FROM messages
             WHERE (from_user_id=? AND to_user_id=?)
                OR (from_user_id=? AND to_user_id=?)
             ORDER BY id',
        );
        $query->execute([$userId, $participantId, $participantId, $userId]);

        return $query->fetchAll();
    }

    public function create(int $fromUserId, int $toUserId, string $text): void
    {
        $query = $this->db->prepare(
            'INSERT INTO messages(from_user_id,to_user_id,text,created_at)VALUES(?,?,?,?)',
        );
        $query->execute([$fromUserId, $toUserId, $text, date('c')]);
    }
}
