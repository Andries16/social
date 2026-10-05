<?php declare(strict_types=1);

namespace Social\Domain\Posts;

final class PostService
{
    private const REACTIONS = ['like', 'love', 'laugh', 'wow', 'sad', 'angry'];

    public function __construct(private PostRepository $posts)
    {
    }

    public function feed(int $viewerId, int $page, int $limit): array
    {
        $result = $this->posts->listForFeed($viewerId, $page, $limit);
        $items = $result['posts'];

        if (count($items) > $limit) {
            array_pop($items);
        }

        foreach ($items as &$post) {
            $post['media'] = $this->posts->media((int) $post['id']);
            $post['reactionCounts'] = [
                'like' => (int) $post['reaction_like'],
                'love' => (int) $post['reaction_love'],
                'laugh' => (int) $post['reaction_laugh'],
                'wow' => (int) $post['reaction_wow'],
                'sad' => (int) $post['reaction_sad'],
                'angry' => (int) $post['reaction_angry'],
            ];
            $post['comments'] = $this->posts->comments((int) $post['id']);
            $post['user'] = [
                'id' => $post['uid'],
                'name' => $post['name'],
                'bio' => $post['bio'],
                'avatar' => $post['avatar'],
            ];
        }

        return [
            'posts' => $items,
            'page' => $page,
            'hasMore' => $result['hasMore'],
        ];
    }

    public function create(int $userId, array $payload): void
    {
        $body = trim((string) ($payload['body'] ?? ''));
        if ($body === '' || mb_strlen($body) > 5000) {
            throw new \InvalidArgumentException(
                'Post text must contain between 1 and 5000 characters',
            );
        }

        $media = array_values(array_filter(array_map('strval', (array) ($payload['media'] ?? []))));
        if (count($media) > 10) throw new \InvalidArgumentException('A post can contain at most 10 images');
        $image = trim((string) ($payload['image'] ?? ($media[0] ?? '')));
        $this->posts->create($userId, $body, $image, $media);
    }

    public function update(int $userId, int $postId, array $payload): void
    {
        $this->authorizeOwner($userId, $postId);
        $body = trim((string) ($payload['body'] ?? ''));
        if ($body === '' || mb_strlen($body) > 5000) {
            throw new \InvalidArgumentException(
                'Post text must contain between 1 and 5000 characters',
            );
        }

        $media = array_values(array_filter(array_map('strval', (array) ($payload['media'] ?? []))));
        if (count($media) > 10) throw new \InvalidArgumentException('A post can contain at most 10 images');
        $image = trim((string) ($payload['image'] ?? ($media[0] ?? '')));
        $this->posts->update($postId, $body, $image);
        $this->posts->replaceMedia($postId, $media);
    }

    public function delete(int $userId, int $postId): void
    {
        $this->authorizeOwner($userId, $postId);
        $this->posts->delete($postId);
    }

    public function react(int $userId, int $postId, array $payload): void
    {
        if (!$this->posts->canView($postId, $userId)) {
            throw new \RuntimeException('Post not found');
        }

        $reaction = (string) ($payload['reaction'] ?? 'like');
        if (!in_array($reaction, self::REACTIONS, true)) {
            throw new \InvalidArgumentException('Unsupported reaction');
        }

        $this->posts->react($postId, $userId, $reaction);
    }

    public function comment(int $userId, int $postId, array $payload): void
    {
        if (!$this->posts->canView($postId, $userId)) {
            throw new \RuntimeException('Post not found');
        }

        $text = trim((string) ($payload['text'] ?? ''));
        if ($text === '' || mb_strlen($text) > 2000) {
            throw new \InvalidArgumentException(
                'Comment must contain between 1 and 2000 characters',
            );
        }

        $this->posts->createComment($postId, $userId, $text);
    }

    public function updateComment(int $userId, int $commentId, array $payload): void
    {
        $this->authorizeCommentOwner($userId, $commentId);
        $text = trim((string) ($payload['text'] ?? ''));
        if ($text === '' || mb_strlen($text) > 2000) {
            throw new \InvalidArgumentException(
                'Comment must contain between 1 and 2000 characters',
            );
        }

        $this->posts->updateComment($commentId, $text);
    }

    public function deleteComment(int $userId, int $commentId): void
    {
        $this->authorizeCommentOwner($userId, $commentId);
        $this->posts->deleteComment($commentId);
    }

    private function authorizeOwner(int $userId, int $postId): void
    {
        $ownerId = $this->posts->findOwnerId($postId);
        if ($ownerId === null) {
            throw new \RuntimeException('Post not found');
        }
        if ($ownerId !== $userId) {
            throw new \LogicException('Forbidden');
        }
    }

    private function authorizeCommentOwner(int $userId, int $commentId): void
    {
        $ownerId = $this->posts->findCommentOwnerId($commentId);
        if ($ownerId === null) {
            throw new \RuntimeException('Comment not found');
        }
        if ($ownerId !== $userId) {
            throw new \LogicException('Forbidden');
        }
    }
}
