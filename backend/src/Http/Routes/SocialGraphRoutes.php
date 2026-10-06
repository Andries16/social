<?php declare(strict_types=1);

function handleSocialGraphRoutes(string $r, string $m): bool
{
    global $db;

    $graph = new \Social\Domain\SocialGraph\SocialGraphService(
        new \Social\Domain\SocialGraph\SocialGraphRepository($db),
    );

    try {
        if (preg_match('#^/api/users/(\d+)/follow$#', $r, $x) && in_array($m, ['GET', 'POST', 'DELETE'], true)) {
            $u = me();
            $targetId = (int) $x[1];

            if ($m === 'GET') {
                out(['following' => $graph->status((int) $u['id'], $targetId)]);
            }

            if ($m === 'POST') {
                $created = $graph->follow((int) $u['id'], $targetId);

                if ($created) {
                    $notifications = new \Social\Domain\Notifications\NotificationService(
                        new \Social\Domain\Notifications\NotificationRepository($db),
                    );
                    $notifications->notify(
                        $targetId,
                        'started_following_you',
                        (int) $u['id'],
                    );
                }

                out(['ok' => true]);
            }

            $graph->unfollow((int) $u['id'], $targetId);
            out(['ok' => true]);
        }

        if (
            preg_match('#^/api/users/(\d+)/(followers|following)$#', $r, $x)
            && $m === 'GET'
        ) {
            me();
            out([
                'users' => $graph->list((int) $x[1], $x[2]),
            ]);
        }
    } catch (\InvalidArgumentException $e) {
        out(['error' => $e->getMessage()], 422);
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 404);
    }

    return false;
}
