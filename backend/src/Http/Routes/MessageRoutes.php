<?php declare(strict_types=1);

function handleMessageRoutes(string $r, string $m): bool
{
    global $db;

    $messages = new \Social\Domain\Messages\MessageService(
        new \Social\Domain\Messages\MessageRepository($db),
    );

    try {
        if ($r === '/api/messages' && $m === 'GET') {
            $u = me();
            $participantId = (int) ($_GET['user_id'] ?? 0);
            out([
                'messages' => $messages->conversation((int) $u['id'], $participantId),
            ]);
        }

        if ($r === '/api/messages' && $m === 'POST') {
            $u = me();
            rateLimit('message', (string) $u['id'], 120, 3600);
            $participantId = $messages->send((int) $u['id'], b());

            $notification = $db->prepare(
                'INSERT INTO notifications(user_id,type,actor_id,created_at)VALUES(?,?,?,?)',
            );
            $notification->execute([
                $participantId,
                'sent_you_a_message',
                $u['id'],
                date('c'),
            ]);

            out(['ok' => true]);
        }
    } catch (\InvalidArgumentException $e) {
        out(['error' => $e->getMessage()], 422);
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 404);
    }

    return false;
}
