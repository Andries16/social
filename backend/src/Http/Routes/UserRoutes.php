<?php declare(strict_types=1);

function handleUserRoutes(string $r, string $m): bool
{
    global $db;

    $userService = new \Social\Domain\Users\UserService(
        new \Social\Domain\Users\UserRepository($db),
    );

    try {
        if (preg_match('#^/api/users/(\d+)$#', $r, $x) && $m === 'GET') {
            $u = me();
            out($userService->profile((int) $u['id'], (int) $x[1]));
        }

        if ($r === '/api/users' && $m === 'GET') {
            $u = me();
            $term = (string) ($_GET['q'] ?? '');
            $limit = (int) ($_GET['limit'] ?? 20);
            out([
                'users' => $userService->search((int) $u['id'], $term, $limit),
            ]);
        }
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 404);
    }

    return false;
}
