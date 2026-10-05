<?php declare(strict_types=1);

function handleNotificationRoutes(string $r, string $m): bool
{
    global $db;

    $notifications = new \Social\Domain\Notifications\NotificationService(
        new \Social\Domain\Notifications\NotificationRepository($db),
    );

    try {
        if ($r === '/api/notifications' && $m === 'GET') {
            $u = me();
            out($notifications->list((int) $u['id']));
        }

        if (preg_match('#^/api/notifications/(\\d+)/read$#', $r, $x) && $m === 'POST') {
            $u = me();
            $notifications->markRead((int) $u['id'], (int) $x[1]);
            out(['ok' => true]);
        }
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 404);
    }

    return false;
}
