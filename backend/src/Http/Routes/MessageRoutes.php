<?php declare(strict_types=1);

function handleMessageRoutes(string $r, string $m): bool
{
    global $db;
    $messages = new \Social\Domain\Messages\MessageService(new \Social\Domain\Messages\MessageRepository($db));
    $notifications = new \Social\Domain\Notifications\NotificationService(new \Social\Domain\Notifications\NotificationRepository($db));

    try {
        if ($r === '/api/messages' && $m === 'GET') {
            $u = me();
            $participantId = (int) ($_GET['user_id'] ?? 0);
            out(['messages' => $messages->conversation((int) $u['id'], $participantId)]);
        }
        if ($r === '/api/messages' && $m === 'POST') {
            $u = me();
            rateLimit('message', (string) $u['id'], 120, 3600);
            $participantId = $messages->send((int) $u['id'], b());
            $notifications->notify($participantId, 'sent_you_a_message', (int) $u['id']);
            out(['ok' => true]);
        }
    } catch (\InvalidArgumentException $e) {
        out(['error' => $e->getMessage()], 422);
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 404);
    }
    return false;
}
