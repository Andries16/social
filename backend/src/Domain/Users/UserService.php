<?php declare(strict_types=1);

namespace Social\Domain\Users;

final class UserService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function profile(int $viewerId, int $targetId): array
    {
        $user = $this->users->findById($targetId);
        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        $private = $this->users->isPrivate($targetId);
        $isOwner = $viewerId === $targetId;
        $following = $isOwner || $this->users->isFollowing($viewerId, $targetId);
        $canViewPosts = !$private || $following;

        if (!$isOwner) {
            $user['email'] = null;
        }
        if (!$canViewPosts) {
            $user['bio'] = '';
        }

        return [
            'user' => $user,
            'private_account' => $private,
            'can_view_posts' => $canViewPosts,
        ];
    }
}
