<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        // Charge les relations nécessaires. IMPORTANT : les relations sont chargées ici,
        // mais broadcastWith va les convertir en tableaux.
        $this->message = $message->load('user', 'attachments');
    }

    public function broadcastOn(): array
    {

        return [
            new PresenceChannel('chat'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'user' => $this->message->user->toArray(),
            'body' => $this->message->body,

            'attachments' => $this->message->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'file_path' => $attachment->file_path,
                    'file_name' => $attachment->file_name,
                    'file_mime_type' => $attachment->file_mime_type,
                    'file_size' => $attachment->file_size,
                    'url' => $attachment->url,
                ];
            })->toArray(),
            'created_at' => $this->message->created_at->diffForHumans(),
        ];
    }
}
