<?php declare(strict_types=1);

namespace Social\Domain\Notifications;

final class NotificationRepository
{
    public function __construct(private \PDO $db)
    {
    }

    public function listForUser(int $userId, int $limit): array
    {
        $query = $this->db->prepare(
            'SELECT n.*,u.id uid,u.name,u.avatar,u.bio
             FROM notifications n
             JOIN users u ON u.id=n.actor_id
             WHERE n.user_id=?
             ORDER BY n.id DESC
             LIMIT ?',
        );
        $query->bindValue(1, $userId, \PDO::PARAM_INT);
        $query->bindValue(2, $limit, \PDO::PARAM_INT);
        $query->execute();
        $items = $query->fetchAll();

        foreach ($items as &$item) {
            $item['actor'] = [
                'id' => $item['uid'],
                'name' => $item['name'],
                'avatar' => $item['avatar'],
                'bio' => $item['bio'],
            ];
        }

        return $items;
    }

    public function markRead(int $notificationId, int $userId): void
    {
        $query = $this->db->prepare(
            'UPDATE notifications SET read_at=? WHERE id=? AND user_id=?',
        );
        $query->execute([date('c'), $notificationId, $userId]);
    }

    public function belongsToUser(int $notificationId, int $userId): bool
    {
        $query = $this->db->prepare(
            'SELECT 1 FROM notifications WHERE id=? AND user_id=?',
        );
        $query->execute([$notificationId, $userId]);

        return (bool) $query->fetchColumn();
    }
}
