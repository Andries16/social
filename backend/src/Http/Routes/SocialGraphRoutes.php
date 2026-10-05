<?php declare(strict_types=1);

function handleSocialGraphRoutes(string $r, string $m): bool
{
    global $db;

    $graph = new \Social\Domain\SocialGraph\SocialGraphService(
        new \Social\Domain\SocialGraph\SocialGraphRepository($db),
    );

    try {
        if (preg_match('#^/api/users/(\\d+)/follow$#', $r, $x) && in_array($m, ['POST', 'DELETE'], true)) {
            $u = me();
            $targetId = (int) $x[1];

            if ($m === 'POST') {
                $created = $graph->follow((int) $u['id'], $targetId);

                if ($created) {
                    $notification = $db->prepare(
                        'INSERT INTO notifications(user_id,type,actor_id,created_at)VALUES(?,?,?,?)',
                    );
                    $notification->execute([
                        $targetId,
                        'started_following_you',
                        $u['id'],
                        date('c'),
                    ]);
                }

                out(['ok' => true]);
            }

            $graph->unfollow((int) $u['id'], $targetId);
            out(['ok' => true]);
        }

        if (
            preg_match('#^/api/users/(\\d+)/(followers|following)$#', $r, $x)
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
