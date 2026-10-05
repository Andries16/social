<?php declare(strict_types=1);

function handlePostRoutes(string $r, string $m): bool
{
    global $db;

    $posts = new \Social\Domain\Posts\PostService(
        new \Social\Domain\Posts\PostRepository($db),
    );

    try {
        if ($r === '/api/posts' && $m === 'GET') {
            $u = me();
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $limit = min(50, max(1, (int) ($_GET['limit'] ?? 10)));

            out($posts->feed((int) $u['id'], $page, $limit));
        }

        if ($r === '/api/posts' && $m === 'POST') {
            $u = me();
            rateLimit('post', (string) $u['id'], 30, 3600);
            $posts->create((int) $u['id'], b());
            out(['ok' => true]);
        }

        if (preg_match('#^/api/posts/(\\d+)$#', $r, $x) && in_array($m, ['PATCH', 'DELETE'], true)) {
            $u = me();
            $postId = (int) $x[1];

            if ($m === 'DELETE') {
                $posts->delete((int) $u['id'], $postId);
                out(['ok' => true]);
            }

            $posts->update((int) $u['id'], $postId, b());
            out(['ok' => true]);
        }

        if (preg_match('#^/api/posts/(\\d+)/react$#', $r, $x) && $m === 'POST') {
            $u = me();
            $postId = (int) $x[1];
            $posts->react((int) $u['id'], $postId, b());

            $owner = $db->prepare('SELECT user_id FROM posts WHERE id=?');
            $owner->execute([$postId]);
            $ownerId = (int) $owner->fetchColumn();
            if ($ownerId && $ownerId !== (int) $u['id']) {
                $notification = $db->prepare(
                    'INSERT INTO notifications(user_id,type,actor_id,post_id,created_at)
                     VALUES(?,?,?,?,?)',
                );
                $notification->execute([
                    $ownerId,
                    'reacted_to_post',
                    $u['id'],
                    $postId,
                    date('c'),
                ]);
            }

            out(['ok' => true]);
        }

        if (preg_match('#^/api/posts/(\\d+)/comments$#', $r, $x) && $m === 'POST') {
            $u = me();
            $postId = (int) $x[1];
            rateLimit('comment', (string) $u['id'], 60, 3600);
            $posts->comment((int) $u['id'], $postId, b());

            $owner = $db->prepare('SELECT user_id FROM posts WHERE id=?');
            $owner->execute([$postId]);
            $ownerId = (int) $owner->fetchColumn();
            if ($ownerId && $ownerId !== (int) $u['id']) {
                $notification = $db->prepare(
                    'INSERT INTO notifications(user_id,type,actor_id,post_id,created_at)
                     VALUES(?,?,?,?,?)',
                );
                $notification->execute([
                    $ownerId,
                    'commented_on_post',
                    $u['id'],
                    $postId,
                    date('c'),
                ]);
            }

            out(['ok' => true]);
        }

        if (preg_match('#^/api/comments/(\\d+)$#', $r, $x) && in_array($m, ['PATCH', 'DELETE'], true)) {
            $u = me();
            $commentId = (int) $x[1];

            if ($m === 'DELETE') {
                $posts->deleteComment((int) $u['id'], $commentId);
                out(['ok' => true]);
            }

            $posts->updateComment((int) $u['id'], $commentId, b());
            out(['ok' => true]);
        }
    } catch (\InvalidArgumentException $e) {
        out(['error' => $e->getMessage()], 422);
    } catch (\LogicException $e) {
        out(['error' => $e->getMessage()], 403);
    } catch (\RuntimeException $e) {
        out(['error' => $e->getMessage()], 404);
    }

    return false;
}
