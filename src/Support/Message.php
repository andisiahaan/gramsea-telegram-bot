<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Wrapper untuk Telegram Message object.
 * 
 * @see https://core.telegram.org/bots/api#message
 */
class Message
{
    protected array $data;
    protected ?Chat $chat = null;
    protected ?User $from = null;
    protected ?User $forwardFrom = null;
    protected ?Chat $forwardFromChat = null;
    protected ?User $viaBot = null;
    protected ?Message $replyToMessage = null;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get raw data array.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    // =========================================================================
    // BASIC MESSAGE INFO
    // =========================================================================

    /**
     * Message ID.
     */
    public function id(): int
    {
        return $this->data['message_id'] ?? 0;
    }

    /**
     * Alias untuk id().
     */
    public function messageId(): int
    {
        return $this->id();
    }

    /**
     * Message thread ID (for forum topics).
     */
    public function threadId(): ?int
    {
        return $this->data['message_thread_id'] ?? null;
    }

    /**
     * Date message was sent (Unix timestamp).
     */
    public function date(): int
    {
        return $this->data['date'] ?? 0;
    }

    /**
     * Date as DateTime object.
     */
    public function dateTime(): \DateTime
    {
        return (new \DateTime())->setTimestamp($this->date());
    }

    /**
     * Edit date (Unix timestamp, jika pesan diedit).
     */
    public function editDate(): ?int
    {
        return $this->data['edit_date'] ?? null;
    }

    // =========================================================================
    // CHAT & USER
    // =========================================================================

    /**
     * Chat object.
     */
    public function chat(): Chat
    {
        if ($this->chat === null) {
            $this->chat = new Chat($this->data['chat'] ?? []);
        }
        return $this->chat;
    }

    /**
     * Chat ID (shortcut).
     */
    public function chatId(): int
    {
        return $this->chat()->id();
    }

    /**
     * Chat type (shortcut).
     */
    public function chatType(): string
    {
        return $this->chat()->type();
    }

    /**
     * Chat title atau name (shortcut).
     */
    public function chatName(): string
    {
        return $this->chat()->name();
    }

    /**
     * Chat username (shortcut).
     */
    public function chatUsername(): ?string
    {
        return $this->chat()->username();
    }

    /**
     * From user (pengirim pesan).
     */
    public function from(): ?User
    {
        if ($this->from === null && isset($this->data['from'])) {
            $this->from = new User($this->data['from']);
        }
        return $this->from;
    }

    /**
     * From user ID (shortcut).
     */
    public function fromId(): ?int
    {
        return $this->from()?->id();
    }

    /**
     * From username (shortcut).
     */
    public function fromUsername(): ?string
    {
        return $this->from()?->username();
    }

    /**
     * From full name (shortcut).
     */
    public function fromName(): ?string
    {
        return $this->from()?->fullName();
    }

    /**
     * Sender chat (untuk channel posts atau anonymous group messages).
     */
    public function senderChat(): ?Chat
    {
        if (isset($this->data['sender_chat'])) {
            return new Chat($this->data['sender_chat']);
        }
        return null;
    }

    // =========================================================================
    // TEXT CONTENT
    // =========================================================================

    /**
     * Text content.
     */
    public function text(): ?string
    {
        return $this->data['text'] ?? null;
    }

    /**
     * Check if message has text.
     */
    public function hasText(): bool
    {
        return !empty($this->data['text']);
    }

    /**
     * Caption (untuk media).
     */
    public function caption(): ?string
    {
        return $this->data['caption'] ?? null;
    }

    /**
     * Check if message has caption.
     */
    public function hasCaption(): bool
    {
        return !empty($this->data['caption']);
    }

    /**
     * Get text atau caption.
     */
    public function textOrCaption(): ?string
    {
        return $this->text() ?? $this->caption();
    }

    /**
     * Text entities.
     */
    public function entities(): array
    {
        return $this->data['entities'] ?? [];
    }

    /**
     * Caption entities.
     */
    public function captionEntities(): array
    {
        return $this->data['caption_entities'] ?? [];
    }

    // =========================================================================
    // MEDIA CONTENT
    // =========================================================================

    /**
     * Photo array.
     */
    public function photo(): ?array
    {
        return $this->data['photo'] ?? null;
    }

    /**
     * Get largest photo.
     */
    public function largestPhoto(): ?array
    {
        $photos = $this->photo();
        if (empty($photos)) {
            return null;
        }
        return end($photos);
    }

    /**
     * Check if message has photo.
     */
    public function hasPhoto(): bool
    {
        return !empty($this->data['photo']);
    }

    /**
     * Video object.
     */
    public function video(): ?array
    {
        return $this->data['video'] ?? null;
    }

    /**
     * Check if message has video.
     */
    public function hasVideo(): bool
    {
        return !empty($this->data['video']);
    }

    /**
     * Audio object.
     */
    public function audio(): ?array
    {
        return $this->data['audio'] ?? null;
    }

    /**
     * Check if message has audio.
     */
    public function hasAudio(): bool
    {
        return !empty($this->data['audio']);
    }

    /**
     * Document object.
     */
    public function document(): ?array
    {
        return $this->data['document'] ?? null;
    }

    /**
     * Check if message has document.
     */
    public function hasDocument(): bool
    {
        return !empty($this->data['document']);
    }

    /**
     * Animation (GIF).
     */
    public function animation(): ?array
    {
        return $this->data['animation'] ?? null;
    }

    /**
     * Check if message has animation.
     */
    public function hasAnimation(): bool
    {
        return !empty($this->data['animation']);
    }

    /**
     * Voice message.
     */
    public function voice(): ?array
    {
        return $this->data['voice'] ?? null;
    }

    /**
     * Check if message has voice.
     */
    public function hasVoice(): bool
    {
        return !empty($this->data['voice']);
    }

    /**
     * Video note (round video).
     */
    public function videoNote(): ?array
    {
        return $this->data['video_note'] ?? null;
    }

    /**
     * Check if message has video note.
     */
    public function hasVideoNote(): bool
    {
        return !empty($this->data['video_note']);
    }

    /**
     * Sticker.
     */
    public function sticker(): ?array
    {
        return $this->data['sticker'] ?? null;
    }

    /**
     * Check if message has sticker.
     */
    public function hasSticker(): bool
    {
        return !empty($this->data['sticker']);
    }

    /**
     * Check if message has any media.
     */
    public function hasMedia(): bool
    {
        return $this->hasPhoto() || $this->hasVideo() || $this->hasAudio() ||
               $this->hasDocument() || $this->hasAnimation() || $this->hasVoice() ||
               $this->hasVideoNote() || $this->hasSticker();
    }

    /**
     * Get media type.
     */
    public function mediaType(): ?string
    {
        if ($this->hasPhoto()) return 'photo';
        if ($this->hasVideo()) return 'video';
        if ($this->hasAudio()) return 'audio';
        if ($this->hasDocument()) return 'document';
        if ($this->hasAnimation()) return 'animation';
        if ($this->hasVoice()) return 'voice';
        if ($this->hasVideoNote()) return 'video_note';
        if ($this->hasSticker()) return 'sticker';
        return null;
    }

    // =========================================================================
    // LOCATION & CONTACT
    // =========================================================================

    /**
     * Location.
     */
    public function location(): ?array
    {
        return $this->data['location'] ?? null;
    }

    /**
     * Check if message has location.
     */
    public function hasLocation(): bool
    {
        return !empty($this->data['location']);
    }

    /**
     * Contact.
     */
    public function contact(): ?array
    {
        return $this->data['contact'] ?? null;
    }

    /**
     * Check if message has contact.
     */
    public function hasContact(): bool
    {
        return !empty($this->data['contact']);
    }

    /**
     * Venue.
     */
    public function venue(): ?array
    {
        return $this->data['venue'] ?? null;
    }

    /**
     * Poll.
     */
    public function poll(): ?array
    {
        return $this->data['poll'] ?? null;
    }

    /**
     * Dice.
     */
    public function dice(): ?array
    {
        return $this->data['dice'] ?? null;
    }

    // =========================================================================
    // REPLY & FORWARD
    // =========================================================================

    /**
     * Reply to message.
     */
    public function replyToMessage(): ?Message
    {
        if ($this->replyToMessage === null && isset($this->data['reply_to_message'])) {
            $this->replyToMessage = new Message($this->data['reply_to_message']);
        }
        return $this->replyToMessage;
    }

    /**
     * Check if this is a reply.
     */
    public function isReply(): bool
    {
        return isset($this->data['reply_to_message']);
    }

    /**
     * Forward origin.
     */
    public function forwardOrigin(): ?array
    {
        return $this->data['forward_origin'] ?? null;
    }

    /**
     * Check if message is forwarded.
     */
    public function isForwarded(): bool
    {
        return isset($this->data['forward_origin']) || isset($this->data['forward_date']);
    }

    /**
     * Forward date.
     */
    public function forwardDate(): ?int
    {
        return $this->data['forward_date'] ?? null;
    }

    // =========================================================================
    // KEYBOARD & INTERACTIONS
    // =========================================================================

    /**
     * Reply markup.
     */
    public function replyMarkup(): ?array
    {
        return $this->data['reply_markup'] ?? null;
    }

    /**
     * Check if message has inline keyboard.
     */
    public function hasInlineKeyboard(): bool
    {
        return isset($this->data['reply_markup']['inline_keyboard']);
    }

    // =========================================================================
    // SPECIAL MESSAGE TYPES
    // =========================================================================

    /**
     * New chat members.
     */
    public function newChatMembers(): array
    {
        $members = $this->data['new_chat_members'] ?? [];
        return array_map(fn($m) => new User($m), $members);
    }

    /**
     * Check if this is a new member join message.
     */
    public function hasNewChatMembers(): bool
    {
        return !empty($this->data['new_chat_members']);
    }

    /**
     * Left chat member.
     */
    public function leftChatMember(): ?User
    {
        if (isset($this->data['left_chat_member'])) {
            return new User($this->data['left_chat_member']);
        }
        return null;
    }

    /**
     * Check if this is a member left message.
     */
    public function hasLeftChatMember(): bool
    {
        return isset($this->data['left_chat_member']);
    }

    /**
     * New chat title.
     */
    public function newChatTitle(): ?string
    {
        return $this->data['new_chat_title'] ?? null;
    }

    /**
     * New chat photo.
     */
    public function newChatPhoto(): ?array
    {
        return $this->data['new_chat_photo'] ?? null;
    }

    /**
     * Check if chat photo was deleted.
     */
    public function deleteChatPhoto(): bool
    {
        return $this->data['delete_chat_photo'] ?? false;
    }

    /**
     * Check if group was created.
     */
    public function groupChatCreated(): bool
    {
        return $this->data['group_chat_created'] ?? false;
    }

    /**
     * Check if supergroup was created.
     */
    public function supergroupChatCreated(): bool
    {
        return $this->data['supergroup_chat_created'] ?? false;
    }

    /**
     * Check if channel was created.
     */
    public function channelChatCreated(): bool
    {
        return $this->data['channel_chat_created'] ?? false;
    }

    /**
     * Pinned message.
     */
    public function pinnedMessage(): ?Message
    {
        if (isset($this->data['pinned_message'])) {
            return new Message($this->data['pinned_message']);
        }
        return null;
    }

    /**
     * Check if this is a service message (non-content message).
     */
    public function isServiceMessage(): bool
    {
        return $this->hasNewChatMembers() ||
               $this->hasLeftChatMember() ||
               $this->newChatTitle() !== null ||
               $this->newChatPhoto() !== null ||
               $this->deleteChatPhoto() ||
               $this->groupChatCreated() ||
               $this->supergroupChatCreated() ||
               $this->channelChatCreated() ||
               $this->pinnedMessage() !== null;
    }

    // =========================================================================
    // COMMAND PARSING
    // =========================================================================

    /**
     * Check if message is a bot command.
     */
    public function isCommand(): bool
    {
        $text = $this->text();
        return $text !== null && str_starts_with($text, '/');
    }

    /**
     * Get command name (without /).
     */
    public function command(): ?string
    {
        if (!$this->isCommand()) {
            return null;
        }

        $text = $this->text();
        // Handle /command@botname format
        preg_match('/^\/([a-zA-Z0-9_]+)/', $text, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Get command arguments (text after command).
     */
    public function commandArgs(): ?string
    {
        if (!$this->isCommand()) {
            return null;
        }

        $text = $this->text();
        // Remove command part
        $args = preg_replace('/^\/[a-zA-Z0-9_]+(@[a-zA-Z0-9_]+)?/', '', $text);
        $args = trim($args);
        return $args !== '' ? $args : null;
    }

    /**
     * Get command arguments as array.
     */
    public function commandArgsArray(): array
    {
        $args = $this->commandArgs();
        if ($args === null) {
            return [];
        }
        return preg_split('/\s+/', $args);
    }

    // =========================================================================
    // MAGIC METHODS
    // =========================================================================

    /**
     * Magic getter untuk akses property langsung.
     */
    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Check if property exists.
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }
}
