<?php declare(strict_types=1);

namespace Social\Domain\Auth;

final class AuthService
{
    public function __construct(private AuthRepository $auth, private \Social\Domain\Media\MediaService $media)
    {
    }

    public function validateRegistration(array $payload): array
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $password = (string) ($payload['password'] ?? '');

        if ($name === '' || mb_strlen($name) > 120) {
            throw new \InvalidArgumentException('Name must contain between 1 and 120 characters');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
            throw new \InvalidArgumentException('A valid email address is required');
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must contain at least 6 characters');
        }

        return [$name, $email, $password];
    }

    public function authenticate(string $email, string $password): array
    {
        $user = $this->auth->findByEmail(strtolower(trim($email)));

        if ($user === null || !password_verify($password, $user['password'])) {
            throw new \RuntimeException('Invalid credentials');
        }

        return $user;
    }

    public function register(array $payload): int
    {
        [$name, $email, $password] = $this->validateRegistration($payload);

        if ($this->auth->emailExists($email)) {
            throw new \Social\Domain\Shared\ConflictException('An account with this email already exists');
        }

        return $this->auth->createUser(
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
        );
    }

    public function profileUpdate(int $userId, array $payload, array $currentUser): void
    {
        $bio = array_key_exists('bio', $payload)
            ? trim((string) $payload['bio'])
            : (string) $currentUser['bio'];
        $avatar = array_key_exists('avatar', $payload)
            ? trim((string) $payload['avatar'])
            : (string) $currentUser['avatar'];

        if (mb_strlen($bio) > 500) {
            throw new \InvalidArgumentException('Bio must be 500 characters or fewer');
        }

        if (mb_strlen($avatar) > 2048) {
            throw new \InvalidArgumentException('Avatar URL is too long');
        }

        if (array_key_exists('avatar', $payload) && $avatar !== '') {
            $ownedAvatar = $this->media->ownedUrl($userId, $avatar);
            if ($ownedAvatar === null) {
                throw new \InvalidArgumentException('Avatar must reference an upload owned by the current user');
            }
            $avatar = $ownedAvatar;
        }

        $this->auth->updateProfile($userId, $bio, $avatar);
    }

    public function settings(int $userId): array
    {
        return ['settings' => $this->auth->settings($userId)];
    }

    public function updateSettings(int $userId, array $payload): void
    {
        $this->auth->saveSettings(
            $userId,
            !empty($payload['private_account']),
            !empty($payload['email_notifications']),
        );
    }
}
