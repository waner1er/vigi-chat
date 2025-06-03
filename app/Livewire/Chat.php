<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Message;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Chat extends Component
{
    use WithFileUploads;

    // Déclarez $messages comme un tableau
    public array $messages = [];
    public $newMessage = '';
    public $fileUploads = [];

    // Écouteur pour les événements broadcastés
    protected $listeners = ['echo-presence:chat,MessageSent' => 'addMessage'];

    public function mount()
    {
        // Récupérez les messages, chargez les relations, et convertissez tout en tableaux plats
        $this->messages = Message::with('user', 'attachments')
            ->latest()
            ->take(50)
            ->get()
            ->reverse() // Pour les avoir du plus ancien au plus récent
            ->map(function ($message) {
                // Convertir chaque message en un tableau de données, comme broadcastWith
                return [
                    'id' => $message->id,
                    'user' => $message->user->toArray(),
                    'body' => $message->body,
                    'attachments' => $message->attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_name' => $attachment->file_name,
                            'file_mime_type' => $attachment->file_mime_type,
                            'url' => $attachment->url,
                            'file_size' => $attachment->file_size,
                        ];
                    })->toArray(),
                    'created_at' => $message->created_at->diffForHumans(),
                ];
            })->toArray(); // Convertir la collection finale en tableau
    }

    public function addMessage($data)
    {

        $this->messages[] = $data;
        $this->dispatch('messageAdded');
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'nullable|string|max:20000',
            'fileUploads.*' => 'nullable|file|max:1048576
',
        ]);

        if (empty($this->newMessage) && empty($this->fileUploads)) {
            return;
        }

        $message = auth()->user()->messages()->create([
            'body' => $this->newMessage,
        ]);

        foreach ($this->fileUploads as $file) {
            $path = $file->store('chat_attachments', 'public');

            $message->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        event(new MessageSent($message));

        $this->newMessage = '';
        $this->fileUploads = [];
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
