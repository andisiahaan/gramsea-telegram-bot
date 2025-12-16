<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Wrapper untuk Telegram Update object.
 * 
 * Menyediakan akses object-oriented ke data update dari webhook.
 * 
 * @example
 * $update = Update::fromJson(file_get_contents('php://input'));
 * 
 * if ($update->isMessage()) {
 *     $msg = $update->message();
 *     echo $msg->text();
 *     echo $msg->from()->fullName();
 *     echo $msg->chat()->id();
 * }
 * 
 * @see https://core.telegram.org/bots/api#update
 */
class Update
{
    protected array $data;
    protected ?Message $message = null;
    protected ?Message $editedMessage = null;
    protected ?Message $channelPost = null;
    protected ?Message $editedChannelPost = null;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Create Update dari JSON string.
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        return new static($data);
    }

    /**
     * Create Update dari array.
     */
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    /**
     * Create Update dari php://input (webhook).
     */
    public static function fromWebhook(): static
    {
        $input = file_get_contents('php://input');
        if ($input === false || $input === '') {
            throw new \RuntimeException('No webhook data received');
        }
        return static::fromJson($input);
    }

    /**
     * Get raw data array.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Get raw data as JSON.
     */
    public function toJson(): string
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }

    // =========================================================================
    // UPDATE INFO
    // =========================================================================

    /**
     * Update ID.
     */
    public function id(): int
    {
        return $this->data['update_id'] ?? 0;
    }

    /**
     * Alias untuk id().
     */
    public function updateId(): int
    {
        return $this->id();
    }

    // =========================================================================
    // UPDATE TYPE CHECKS
    // =========================================================================

    /**
     * Check if update contains a message.
     */
    public function isMessage(): bool
    {
        return isset($this->data['message']);
    }

    /**
     * Check if update contains an edited message.
     */
    public function isEditedMessage(): bool
    {
        return isset($this->data['edited_message']);
    }

    /**
     * Check if update contains a channel post.
     */
    public function isChannelPost(): bool
    {
        return isset($this->data['channel_post']);
    }

    /**
     * Check if update contains an edited channel post.
     */
    public function isEditedChannelPost(): bool
    {
        return isset($this->data['edited_channel_post']);
    }

    /**
     * Check if update contains any type of message (message, edited, channel, etc).
     */
    public function hasMessage(): bool
    {
        return $this->isMessage() || $this->isEditedMessage() || 
               $this->isChannelPost() || $this->isEditedChannelPost();
    }

    /**
     * Check if update contains a callback query.
     */
    public function isCallbackQuery(): bool
    {
        return isset($this->data['callback_query']);
    }

    /**
     * Check if update contains an inline query.
     */
    public function isInlineQuery(): bool
    {
        return isset($this->data['inline_query']);
    }

    /**
     * Check if update contains a chosen inline result.
     */
    public function isChosenInlineResult(): bool
    {
        return isset($this->data['chosen_inline_result']);
    }

    /**
     * Check if update contains a shipping query.
     */
    public function isShippingQuery(): bool
    {
        return isset($this->data['shipping_query']);
    }

    /**
     * Check if update contains a pre-checkout query.
     */
    public function isPreCheckoutQuery(): bool
    {
        return isset($this->data['pre_checkout_query']);
    }

    /**
     * Check if update contains a poll.
     */
    public function isPoll(): bool
    {
        return isset($this->data['poll']);
    }

    /**
     * Check if update contains a poll answer.
     */
    public function isPollAnswer(): bool
    {
        return isset($this->data['poll_answer']);
    }

    /**
     * Check if update contains my_chat_member.
     */
    public function isMyChatMember(): bool
    {
        return isset($this->data['my_chat_member']);
    }

    /**
     * Check if update contains chat_member.
     */
    public function isChatMember(): bool
    {
        return isset($this->data['chat_member']);
    }

    /**
     * Check if update contains chat_join_request.
     */
    public function isChatJoinRequest(): bool
    {
        return isset($this->data['chat_join_request']);
    }

    /**
     * Get update type as string.
     */
    public function type(): string
    {
        if ($this->isMessage()) return 'message';
        if ($this->isEditedMessage()) return 'edited_message';
        if ($this->isChannelPost()) return 'channel_post';
        if ($this->isEditedChannelPost()) return 'edited_channel_post';
        if ($this->isCallbackQuery()) return 'callback_query';
        if ($this->isInlineQuery()) return 'inline_query';
        if ($this->isChosenInlineResult()) return 'chosen_inline_result';
        if ($this->isShippingQuery()) return 'shipping_query';
        if ($this->isPreCheckoutQuery()) return 'pre_checkout_query';
        if ($this->isPoll()) return 'poll';
        if ($this->isPollAnswer()) return 'poll_answer';
        if ($this->isMyChatMember()) return 'my_chat_member';
        if ($this->isChatMember()) return 'chat_member';
        if ($this->isChatJoinRequest()) return 'chat_join_request';
        return 'unknown';
    }

    // =========================================================================
    // MESSAGE ACCESSORS
    // =========================================================================

    /**
     * Get message object (dari message update).
     */
    public function message(): ?Message
    {
        if ($this->message === null && isset($this->data['message'])) {
            $this->message = new Message($this->data['message']);
        }
        return $this->message;
    }

    /**
     * Get edited message object.
     */
    public function editedMessage(): ?Message
    {
        if ($this->editedMessage === null && isset($this->data['edited_message'])) {
            $this->editedMessage = new Message($this->data['edited_message']);
        }
        return $this->editedMessage;
    }

    /**
     * Get channel post object.
     */
    public function channelPost(): ?Message
    {
        if ($this->channelPost === null && isset($this->data['channel_post'])) {
            $this->channelPost = new Message($this->data['channel_post']);
        }
        return $this->channelPost;
    }

    /**
     * Get edited channel post object.
     */
    public function editedChannelPost(): ?Message
    {
        if ($this->editedChannelPost === null && isset($this->data['edited_channel_post'])) {
            $this->editedChannelPost = new Message($this->data['edited_channel_post']);
        }
        return $this->editedChannelPost;
    }

    /**
     * Get any message (prioritas: message > edited > channel > edited_channel).
     */
    public function anyMessage(): ?Message
    {
        return $this->message() ?? $this->editedMessage() ?? 
               $this->channelPost() ?? $this->editedChannelPost();
    }

    // =========================================================================
    // SHORTCUT METHODS (dari anyMessage)
    // =========================================================================

    /**
     * Get message text (shortcut).
     */
    public function text(): ?string
    {
        return $this->anyMessage()?->text();
    }

    /**
     * Get message ID (shortcut).
     */
    public function messageId(): ?int
    {
        return $this->anyMessage()?->id();
    }

    /**
     * Get chat (shortcut).
     */
    public function chat(): ?Chat
    {
        return $this->anyMessage()?->chat();
    }

    /**
     * Get chat ID (shortcut).
     */
    public function chatId(): ?int
    {
        return $this->anyMessage()?->chatId();
    }

    /**
     * Get from user (shortcut).
     */
    public function from(): ?User
    {
        // For callback queries, from is in callback_query
        if ($this->isCallbackQuery()) {
            $cbData = $this->data['callback_query'];
            return isset($cbData['from']) ? new User($cbData['from']) : null;
        }
        return $this->anyMessage()?->from();
    }

    /**
     * Get from user ID (shortcut).
     */
    public function fromId(): ?int
    {
        return $this->from()?->id();
    }

    /**
     * Get from username (shortcut).
     */
    public function fromUsername(): ?string
    {
        return $this->from()?->username();
    }

    /**
     * Get from full name (shortcut).
     */
    public function fromName(): ?string
    {
        return $this->from()?->fullName();
    }

    // =========================================================================
    // CALLBACK QUERY
    // =========================================================================

    /**
     * Get callback query data.
     */
    public function callbackQuery(): ?array
    {
        return $this->data['callback_query'] ?? null;
    }

    /**
     * Get callback query ID.
     */
    public function callbackQueryId(): ?string
    {
        return $this->data['callback_query']['id'] ?? null;
    }

    /**
     * Get callback data.
     */
    public function callbackData(): ?string
    {
        return $this->data['callback_query']['data'] ?? null;
    }

    /**
     * Get callback query message.
     */
    public function callbackMessage(): ?Message
    {
        if (isset($this->data['callback_query']['message'])) {
            return new Message($this->data['callback_query']['message']);
        }
        return null;
    }

    // =========================================================================
    // INLINE QUERY
    // =========================================================================

    /**
     * Get inline query data.
     */
    public function inlineQuery(): ?array
    {
        return $this->data['inline_query'] ?? null;
    }

    /**
     * Get inline query ID.
     */
    public function inlineQueryId(): ?string
    {
        return $this->data['inline_query']['id'] ?? null;
    }

    /**
     * Get inline query text.
     */
    public function inlineQueryText(): ?string
    {
        return $this->data['inline_query']['query'] ?? null;
    }

    // =========================================================================
    // CHAT MEMBER UPDATES
    // =========================================================================

    /**
     * Get my_chat_member data.
     */
    public function myChatMember(): ?array
    {
        return $this->data['my_chat_member'] ?? null;
    }

    /**
     * Get chat_member data.
     */
    public function chatMember(): ?array
    {
        return $this->data['chat_member'] ?? null;
    }

    /**
     * Get chat_join_request data.
     */
    public function chatJoinRequest(): ?array
    {
        return $this->data['chat_join_request'] ?? null;
    }

    // =========================================================================
    // UTILITY METHODS
    // =========================================================================

    /**
     * Check if message is a command.
     */
    public function isCommand(): bool
    {
        return $this->anyMessage()?->isCommand() ?? false;
    }

    /**
     * Get command name.
     */
    public function command(): ?string
    {
        return $this->anyMessage()?->command();
    }

    /**
     * Get command arguments.
     */
    public function commandArgs(): ?string
    {
        return $this->anyMessage()?->commandArgs();
    }

    /**
     * Check if specific command.
     */
    public function isCommandOf(string $command): bool
    {
        return $this->command() === $command;
    }

    // =========================================================================
    // MAGIC METHODS
    // =========================================================================

    /**
     * Magic getter untuk akses raw data.
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
