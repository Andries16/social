<?php declare(strict_types=1);

function handleUserRoutes(string $r, string $m): bool
{
    global $db;
    $userService = new \Social\Domain\Users\UserService(
        new \Social\Domain\Users\UserRepository($db),
    );

    if (preg_match('#^/api/users/(\d+)$#', $r, $x) && $m === 'GET') {
        $u = me();

        try {
            out($userService->profile((int) $u['id'], (int) $x[1]));
        } catch (\RuntimeException $e) {
            out(['error' => $e->getMessage()], 404);
        }
    }

    if ($r === '/api/users' && $m === 'GET') {
        $u = me();
        $term = trim($_GET['q'] ?? '');
        $limit = min(50, max(1, (int) ($_GET['limit'] ?? 20)));

        $query = $db->prepare(
            'SELECT id,name,bio,avatar FROM users WHERE name LIKE ? ORDER BY name LIMIT ' . $limit,
        );
        $query->execute(['%' . $term . '%']);
        $users = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as &$candidate) {
            $settings = $db->prepare('SELECT private_account FROM user_settings WHERE user_id=?');
            $settings->execute([$candidate['id']]);
            $candidate['private_account'] = (bool) $settings->fetchColumn();

            if ($candidate['private_account'] && $candidate['id'] !== $u['id']) {
                $follow = $db->prepare(
                    'SELECT 1 FROM follows WHERE follower_id=? AND following_id=?',
                );
                $follow->execute([$u['id'], $candidate['id']]);

                if (!$follow->fetchColumn()) {
                    $candidate['bio'] = '';
                }
            }
        }

        out(['users' => $users]);
    }

    return false;
}
