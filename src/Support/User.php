<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Wrapper untuk Telegram User object.
 * 
 * @see https://core.telegram.org/bots/api#user
 */
class User
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
     * User ID.
     */
    public function id(): int
    {
        return $this->data['id'] ?? 0;
    }

    /**
     * Check if user is a bot.
     */
    public function isBot(): bool
    {
        return $this->data['is_bot'] ?? false;
    }

    /**
     * First name.
     */
    public function firstName(): string
    {
        return $this->data['first_name'] ?? '';
    }

    /**
     * Last name (optional).
     */
    public function lastName(): ?string
    {
        return $this->data['last_name'] ?? null;
    }

    /**
     * Full name (first + last).
     */
    public function fullName(): string
    {
        $name = $this->firstName();
        if ($lastName = $this->lastName()) {
            $name .= ' ' . $lastName;
        }
        return $name;
    }

    /**
     * Username without @ (optional).
     */
    public function username(): ?string
    {
        return $this->data['username'] ?? null;
    }

    /**
     * Username with @ prefix.
     */
    public function usernameWithAt(): ?string
    {
        $username = $this->username();
        return $username ? '@' . $username : null;
    }

    /**
     * Language code (optional).
     */
    public function languageCode(): ?string
    {
        return $this->data['language_code'] ?? null;
    }

    /**
     * Check if user is Telegram Premium.
     */
    public function isPremium(): bool
    {
        return $this->data['is_premium'] ?? false;
    }

    /**
     * Check if user added bot to attachment menu.
     */
    public function addedToAttachmentMenu(): bool
    {
        return $this->data['added_to_attachment_menu'] ?? false;
    }

    /**
     * Check if user can join groups.
     */
    public function canJoinGroups(): bool
    {
        return $this->data['can_join_groups'] ?? true;
    }

    /**
     * Check if user can read all group messages.
     */
    public function canReadAllGroupMessages(): bool
    {
        return $this->data['can_read_all_group_messages'] ?? false;
    }

    /**
     * Check if user supports inline queries.
     */
    public function supportsInlineQueries(): bool
    {
        return $this->data['supports_inline_queries'] ?? false;
    }

    /**
     * Get mention link HTML.
     */
    public function mentionHtml(?string $text = null): string
    {
        $displayText = $text ?? $this->fullName();
        return '<a href="tg://user?id=' . $this->id() . '">' . htmlspecialchars($displayText) . '</a>';
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
