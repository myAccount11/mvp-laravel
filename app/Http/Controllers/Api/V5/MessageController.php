<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateMessageRequest;
use App\Http\Requests\V5\UpdateMessageRequest;
use App\Services\V5\MessageService;
use App\Models\V5\MessageAttachment;
use App\Models\V5\Message;
use App\Models\V5\UserSeasonSport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function create(CreateMessageRequest $request): JsonResponse
    {
        try {
            $message = $this->messageService->create($request->validated());
            
            if ($request->hasFile('files')) {
                $attachments = [];
                foreach ($request->file('files') as $file) {
                    $filename = 'attachment-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('uploads', $filename);
                    $attachments[] = [
                        'message_id' => $message->id,
                        'file_path' => $filename,
                    ];
                }
                MessageAttachment::insert($attachments);
            }
            
            return response()->json($message, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAll(): JsonResponse
    {
        $messages = $this->messageService->findAll();
        return response()->json($messages);
    }

    public function getMessagesCount(): JsonResponse
    {
        try {
            $seasonSportId = request('seasonSportId');
            
            // If seasonSportId is 0 or empty, return 0 or handle differently
            if (!$seasonSportId || $seasonSportId == '0' || $seasonSportId == 0) {
                return response()->json(0);
            }
            
            $userIds = UserSeasonSport::where('season_sport_id', $seasonSportId)
                ->pluck('user_id')
                ->toArray();
            
            if (empty($userIds)) {
                return response()->json(0);
            }
            
            $count = Message::whereIn('type_id', [2, 5])
                ->whereIn('user_id', $userIds)
                ->count();
            
            return response()->json($count);
        } catch (\Exception $e) {
            \Log::error('Error in MessageController::getMessagesCount', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'seasonSportId' => request('seasonSportId')
            ]);
            return response()->json([
                'error' => 'Failed to get messages count',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getById(int $id): JsonResponse
    {
        $message = $this->messageService->findOne(['id' => $id]);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }
        return response()->json($message);
    }

    public function update(int $id, UpdateMessageRequest $request): JsonResponse
    {
        try {
            $result = $this->messageService->update($id, $request->validated());
            if (!$result) {
                return response()->json(['error' => 'Message not found'], 404);
            }
            return response()->json(['success' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function remove(int $id): JsonResponse
    {
        $result = $this->messageService->delete($id);
        if (!$result) {
            return response()->json(['error' => 'Message not found'], 404);
        }
        return response()->json(['success' => $result]);
    }
}

