<?php

namespace App\Services\V5;

use App\Models\V5\Message;
use App\Repositories\V5\MessageRepository;
use App\Jobs\SendMailJob;
use App\Models\V5\UserSeasonSport;

class MessageService
{
    protected MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->messageRepository->all();
    }

    public function findOne(array $condition): ?Message
    {
        return $this->messageRepository->findOneBy($condition);
    }

    public function create(array $data): Message
    {
        // In NestJS, this service directly interacts with a queue for sending emails.
        // In Laravel, we can dispatch a job.
        if (isset($data['email']) && $data['email']) {
            SendMailJob::dispatch([
                'to' => $data['email'],
                'subject' => $data['subject'] ?? 'Notification from MVP App',
                'template' => 'emails.notification', // A generic notification email template
                'context' => ['htmlContent' => $data['html']],
            ]);
        }

        return $this->messageRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->messageRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->messageRepository->delete($id);
    }

    public function getMessagesCount(array $data): int
    {
        $userIds = UserSeasonSport::where('season_sport_id', $data['season_sport_id'])
            ->pluck('user_id')
            ->toArray();

        return Message::whereIn('type_id', [2, 5])
            ->whereIn('user_id', $userIds)
            ->count();
    }
}
