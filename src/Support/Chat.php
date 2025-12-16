<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Wrapper untuk Telegram Chat object.
 * 
 * @see https://core.telegram.org/bots/api#chat
 */
class Chat
{
    protected array $data;

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

    /**
     * Chat ID.
     */
    public function id(): int
    {
        return $this->data['id'] ?? 0;
    }

    /**
     * Chat type: private, group, supergroup, channel.
     */
    public function type(): string
    {
        return $this->data['type'] ?? 'private';
    }

    /**
     * Check if chat is private (DM).
     */
    public function isPrivate(): bool
    {
        return $this->type() === 'private';
    }

    /**
     * Check if chat is a group.
     */
    public function isGroup(): bool
    {
        return $this->type() === 'group';
    }

    /**
     * Check if chat is a supergroup.
     */
    public function isSupergroup(): bool
    {
        return $this->type() === 'supergroup';
    }

    /**
     * Check if chat is a channel.
     */
    public function isChannel(): bool
    {
        return $this->type() === 'channel';
    }

    /**
     * Check if chat is any type of group (group or supergroup).
     */
    public function isAnyGroup(): bool
    {
        return $this->isGroup() || $this->isSupergroup();
    }

    /**
     * Title (for groups, supergroups, channels).
     */
    public function title(): ?string
    {
        return $this->data['title'] ?? null;
    }

    /**
     * Username (optional).
     */
    public function username(): ?string
    {
        return $this->data['username'] ?? null;
    }

    /**
     * First name (for private chats).
     */
    public function firstName(): ?string
    {
        return $this->data['first_name'] ?? null;
    }
    
    /**
     * Full name (Check title first, then first + last).
     */
    public function fullName(): string
    {
        $name = $this->title() ?? '';
        if (empty($name)) {
            $name = ($this->firstName() ?? '') . ' ' . ($this->lastName() ?? '');
        }
        return trim($name);
    }
    
    /**
     * Name (Check title first, then first + last).
     */
    public function name(): string
    {
        return $this->fullName();
    }

    /**
     * Last name (for private chats).
     */
    public function lastName(): ?string
    {
        return $this->data['last_name'] ?? null;
    }

    /**
     * Check if chat is a forum.
     */
    public function isForum(): bool
    {
        return $this->data['is_forum'] ?? false;
    }

    /**
     * Get bio (for private chats in getChat response).
     */
    public function bio(): ?string
    {
        return $this->data['bio'] ?? null;
    }

    /**
     * Get description (for groups/channels in getChat response).
     */
    public function description(): ?string
    {
        return $this->data['description'] ?? null;
    }

    /**
     * Get invite link (if available).
     */
    public function inviteLink(): ?string
    {
        return $this->data['invite_link'] ?? null;
    }

    /**
     * Get linked chat ID (for channels linked to a discussion group).
     */
    public function linkedChatId(): ?int
    {
        return $this->data['linked_chat_id'] ?? null;
    }

    /**
     * Get slow mode delay (in seconds).
     */
    public function slowModeDelay(): ?int
    {
        return $this->data['slow_mode_delay'] ?? null;
    }

    /**
     * Check if messages can be auto-deleted.
     */
    public function messageAutoDeleteTime(): ?int
    {
        return $this->data['message_auto_delete_time'] ?? null;
    }

    /**
     * Check if aggressive anti-spam is enabled.
     */
    public function hasAggressiveAntiSpamEnabled(): bool
    {
        return $this->data['has_aggressive_anti_spam_enabled'] ?? false;
    }

    /**
     * Check if hidden members are enabled.
     */
    public function hasHiddenMembers(): bool
    {
        return $this->data['has_hidden_members'] ?? false;
    }

    /**
     * Check if protected content is enabled.
     */
    public function hasProtectedContent(): bool
    {
        return $this->data['has_protected_content'] ?? false;
    }

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
