<?php declare(strict_types=1);

namespace Social\Domain\Auth;

final class AuthRepository
{
    public function __construct(private \PDO $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE email=?');
        $query->execute([$email]);

        return $query->fetch() ?: null;
    }

    public function createUser(string $name, string $email, string $passwordHash): int
    {
        $query = $this->db->prepare(
            'INSERT INTO users(name,email,password,created_at) VALUES(?,?,?,?)',
        );
        $query->execute([$name, $email, $passwordHash, date('c')]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $userId, string $bio, string $avatar): void
    {
        $query = $this->db->prepare('UPDATE users SET bio=?,avatar=? WHERE id=?');
        $query->execute([$bio, $avatar, $userId]);
    }

    public function settings(int $userId): array
    {
        $query = $this->db->prepare(
            'SELECT private_account,email_notifications FROM user_settings WHERE user_id=?',
        );
        $query->execute([$userId]);
        $settings = $query->fetch();

        if ($settings === false) {
            return ['private_account' => false, 'email_notifications' => true];
        }

        return [
            'private_account' => (bool) $settings['private_account'],
            'email_notifications' => (bool) $settings['email_notifications'],
        ];
    }

    public function saveSettings(int $userId, bool $privateAccount, bool $emailNotifications): void
    {
        $query = $this->db->prepare(
            'INSERT INTO user_settings(user_id,private_account,email_notifications)
             VALUES(?,?,?)
             ON CONFLICT(user_id) DO UPDATE SET
               private_account=excluded.private_account,
               email_notifications=excluded.email_notifications',
        );
        $query->execute([$userId, $privateAccount ? 1 : 0, $emailNotifications ? 1 : 0]);
    }
}
