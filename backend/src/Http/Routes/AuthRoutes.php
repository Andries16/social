<?php declare(strict_types=1);

function handleAuthRoutes(string $r, string $m): bool
{
    global $db;
    $auth = new \Social\Domain\Auth\AuthService(
        new \Social\Domain\Auth\AuthRepository($db),
        new \Social\Domain\Media\MediaService(
            new \Social\Domain\Media\MediaRepository($db, __DIR__ . '/../../../uploads'),
        ),
    );

    try {
        if ($r === '/api/register' && $m === 'POST') {
            rateLimit('register', clientKey(), 5, 900);
            $userId = $auth->register(b());
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            out(['user' => me(), 'csrf' => $_SESSION['csrf']]);
        }
        if ($r === '/api/login' && $m === 'POST') {
            rateLimit('login', clientKey(), 10, 900);
            $payload = b();
            $user = $auth->authenticate((string) ($payload['email'] ?? ''), (string) ($payload['password'] ?? ''));
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            out(['user' => me(), 'csrf' => $_SESSION['csrf']]);
        }
        if ($r === '/api/logout' && $m === 'POST') {
            $_SESSION = [];
            session_destroy();
            out(['ok' => true]);
        }
        if ($r === '/api/me' && $m === 'GET') out(['user' => me(), 'csrf' => $_SESSION['csrf']]);
        if ($r === '/api/me' && $m === 'PATCH') {
            $user = me();
            $auth->profileUpdate((int) $user['id'], b(), $user);
            out(['user' => me()]);
        }
        if ($r === '/api/settings' && $m === 'GET') {
            $user = me();
            out($auth->settings((int) $user['id']));
        }
        if ($r === '/api/settings' && $m === 'PATCH') {
            $user = me();
            $auth->updateSettings((int) $user['id'], b());
            out(['ok' => true]);
        }
    } catch (\Social\Domain\Shared\ConflictException $e) {
        out(['error' => $e->getMessage()], 409);
    } catch (\InvalidArgumentException $e) {
        out(['error' => $e->getMessage()], 422);
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 401);
    }

    return false;
}
