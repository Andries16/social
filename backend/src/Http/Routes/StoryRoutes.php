<?php declare(strict_types=1);

function handleStoryRoutes(string $r, string $m): bool
{
    global $db;

    require_once __DIR__ . '/../../Domain/Stories/StoryRepository.php';
    require_once __DIR__ . '/../../Domain/Stories/StoryService.php';

    $stories = new \Social\Domain\Stories\StoryService(
        new \Social\Domain\Stories\StoryRepository($db),
    );

    try {
        if ($r === '/api/stories' && $m === 'GET') {
            $u = me();
            out($stories->list((int) $u['id']));
        }

        if ($r === '/api/stories' && $m === 'POST') {
            $u = me();
            rateLimit('story', (string) $u['id'], 20, 86400);
            $stories->create((int) $u['id'], b());
            out(['ok' => true]);
        }

        if (preg_match('#^/api/stories/(\\d+)/view$#', $r, $x) && $m === 'POST') {
            $u = me();
            $stories->view((int) $u['id'], (int) $x[1]);
            out(['ok' => true]);
        }
    } catch (\InvalidArgumentException $e) {
        out(['error' => $e->getMessage()], 422);
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 404);
    }

    return false;
}
