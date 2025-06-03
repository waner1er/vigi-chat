<div>
    <div class="chat-container" style="max-height: 400px; overflow-y: auto;" x-data="{
        init() {
            Livewire.on('messageAdded', () => {
                this.$nextTick(() => {
                    this.$el.scrollTop = this.$el.scrollHeight;
                });
            });
            this.$el.scrollTop = this.$el.scrollHeight; // Initial scroll to bottom
        }
    }">
        @foreach($messages as $message)
            <div class="message @if($message['user']['id'] === auth()->id()) sent @else received @endif">
                {{-- Accédez aux propriétés comme des clés de tableau --}}
                <strong>{{ $message['user']['name'] }}:</strong> {{ $message['body'] }}
                @foreach($message['attachments'] as $attachment)
                    <div class="attachment">
                        @if(Str::startsWith($attachment['file_mime_type'], 'image'))
                            <img src="{{ $attachment['url'] }}" alt="{{ $attachment['file_name'] }}" style="max-width: 200px;">
                        @elseif(Str::startsWith($attachment['file_mime_type'], 'audio'))
                            <audio controls src="{{ $attachment['url'] }}">
                                Votre navigateur ne supporte pas l'élément audio.
                            </audio>
                        @else
                            <a href="{{ $attachment['url'] }}" target="_blank" download>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-3.625C13.5 2.152 12.339 1.5 11.25 1.5H9.75A1.125 1.125 0 008.625 2.625v11.25a3.375 3.375 0 003.375 3.375h1.5a1.125 1.125 0 001.125-1.125V14.25m-6-6h.008v.008H13.5z" />
                                </svg>
                                {{ $attachment['file_name'] }} ({{ round($attachment['file_size'] / 1024, 2) }} KB)
                            </a>
                        @endif
                    </div>
                @endforeach
                <small>{{ $message['created_at'] }}</small>
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" class="message-input-form">
        <textarea wire:model.live="newMessage" placeholder="Écrire un message..." rows="3"></textarea>
        <input type="file" wire:model="fileUploads" multiple>
        @error('fileUploads.*') <span class="error">{{ $message }}</span> @enderror

        <button type="submit">Envoyer</button>
    </form>
</div>
