<?php declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$r = is_string($path) ? $path : '/';
$m = $method;

if ($path === '/health.php' && $method === 'GET') out(['status' => 'ok']);

if (in_array($method, ['POST', 'PATCH', 'DELETE'], true) && $path !== '/api/login' && $path !== '/api/register') csrf();

require_once __DIR__ . '/src/Http/Routes/AuthRoutes.php';
if (handleAuthRoutes($r, $m)) exit;
require_once __DIR__ . '/src/Http/Routes/UserRoutes.php';
if (handleUserRoutes($r, $m)) exit;
require_once __DIR__ . '/src/Http/Routes/PostRoutes.php';
if (handlePostRoutes($r, $m)) exit;
require_once __DIR__ . '/src/Http/Routes/StoryRoutes.php';
if (handleStoryRoutes($r, $m)) exit;
require_once __DIR__ . '/src/Http/Routes/MessageRoutes.php';
if (handleMessageRoutes($r, $m)) exit;
require_once __DIR__ . '/src/Http/Routes/SocialGraphRoutes.php';
if (handleSocialGraphRoutes($r, $m)) exit;
require_once __DIR__ . '/src/Http/Routes/NotificationRoutes.php';
if (handleNotificationRoutes($r, $m)) exit;

try {
    if ($r === '/api/media' && $m === 'POST') {
        $u = me();
        rateLimit('media', (string) $u['id'], 20, 3600);
        $media = new \Social\Domain\Media\MediaService(
            new \Social\Domain\Media\MediaRepository($db, __DIR__ . '/uploads'),
        );
        $name = $media->upload((int) $u['id'], $_FILES['file'] ?? []);
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $baseUrl = rtrim(getenv('SOCIAL_PUBLIC_URL') ?: $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), '/');
        out(['url' => $baseUrl . '/uploads/' . rawurlencode($name)]);
    }
    out(['error' => 'Not found'], 404);
} catch (\InvalidArgumentException $e) {
    out(['error' => $e->getMessage()], 422);
} catch (Throwable $e) {
    out(['error' => 'Server error'], 500);
}
