<?php declare(strict_types=1);

namespace Social\Domain\Messages;

final class MessageService
{
    public function __construct(private MessageRepository $messages)
    {
    }

    public function conversation(int $userId, int $participantId): array
    {
        $this->validateParticipant($userId, $participantId);

        return $this->messages->conversation($userId, $participantId);
    }

    public function send(int $userId, array $payload): int
    {
        $participantId = (int) ($payload['to_user_id'] ?? 0);
        $text = trim((string) ($payload['text'] ?? ''));

        if (
            $participantId <= 0
            || $participantId === $userId
            || $text === ''
            || mb_strlen($text) > 4000
        ) {
            throw new \InvalidArgumentException(
                'A valid message recipient and text are required',
            );
        }

        $this->validateParticipant($userId, $participantId);
        $this->messages->create($userId, $participantId, $text);

        return $participantId;
    }

    private function validateParticipant(int $userId, int $participantId): void
    {
        if ($participantId <= 0 || $participantId === $userId) {
            throw new \InvalidArgumentException(
                'A conversation participant is required',
            );
        }

        if (!$this->messages->userExists($participantId)) {
            throw new \RuntimeException('User not found');
        }
    }
}
