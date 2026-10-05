<?php declare(strict_types=1);

namespace Social\Domain\Notifications;

final class NotificationService
{
    public function __construct(private NotificationRepository $notifications)
    {
    }

    public function list(int $userId): array
    {
        return ['notifications' => $this->notifications->listForUser($userId, 30)];
    }

    public function notify(int $userId, string $type, int $actorId): void
    {
        if ($userId <= 0 || $actorId <= 0 || $userId === $actorId) {
            return;
        }

        $this->notifications->create($userId, $type, $actorId);
    }

    public function markRead(int $userId, int $notificationId): void
    {
        if ($notificationId <= 0 || !$this->notifications->belongsToUser($notificationId, $userId)) {
            throw new \RuntimeException('Notification not found');
        }

        $this->notifications->markRead($notificationId, $userId);
    }
}
