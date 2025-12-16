<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;

/**
 * Trait berisi helper methods untuk Telegram Bot API.
 */
trait HelperMethods
{
    // =============================================
    // Webhook Methods
    // =============================================

    /**
     * Set webhook URL for the bot.
     *
     * @param string $url HTTPS URL to send updates to
     * @param array $options Optional parameters (certificate, ip_address, max_connections, allowed_updates, drop_pending_updates, secret_token)
     */
    public function setWebhook(string $url, array $options = []): array
    {
        return $this->callMethod('setWebhook', array_merge(['url' => $url], $options));
    }

    /**
     * Remove webhook integration.
     *
     * @param bool $dropPendingUpdates Pass True to drop all pending updates
     */
    public function deleteWebhook(bool $dropPendingUpdates = false): array
    {
        return $this->callMethod('deleteWebhook', ['drop_pending_updates' => $dropPendingUpdates]);
    }

    /**
     * Get current webhook status.
     */
    public function getWebhookInfo(): array
    {
        return $this->callMethod('getWebhookInfo');
    }

    // =============================================
    // Chat Member Check Methods
    // =============================================

    /**
     * Check if a user is a member of a chat (includes creator, admin, member, restricted).
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to check
     */
    public function isChatMember(int|string $chatId, int $userId): bool
    {
        try {
            $member = $this->callMethod('getChatMember', [
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
            $status = $member['result']['status'] ?? '';
            return in_array($status, ['creator', 'administrator', 'member', 'restricted']);
        } catch (ApiException $e) {
            return false;
        }
    }

    /**
     * Check if a user is an administrator of a chat.
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to check
     */
    public function isChatAdmin(int|string $chatId, int $userId): bool
    {
        try {
            $member = $this->callMethod('getChatMember', [
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
            $status = $member['result']['status'] ?? '';
            return in_array($status, ['creator', 'administrator']);
        } catch (ApiException $e) {
            return false;
        }
    }

    /**
     * Check if a user is the creator of a chat.
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to check
     */
    public function isChatCreator(int|string $chatId, int $userId): bool
    {
        try {
            $member = $this->callMethod('getChatMember', [
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
            return ($member['result']['status'] ?? '') === 'creator';
        } catch (ApiException $e) {
            return false;
        }
    }

    /**
     * Get user's chat member status.
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to check
     * @return string|null Status: creator, administrator, member, restricted, left, kicked, or null if error
     */
    public function getChatMemberStatus(int|string $chatId, int $userId): ?string
    {
        try {
            $member = $this->callMethod('getChatMember', [
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
            return $member['result']['status'] ?? null;
        } catch (ApiException $e) {
            return null;
        }
    }

    // =============================================
    // Shortcut Methods
    // =============================================

    /**
     * Send typing action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendTyping(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'typing',
        ]);
    }

    /**
     * Send upload_photo action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendUploadPhotoAction(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'upload_photo',
        ]);
    }

    /**
     * Send upload_document action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendUploadDocumentAction(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'upload_document',
        ]);
    }

    /**
     * Send upload_video action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendUploadVideoAction(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'upload_video',
        ]);
    }

    /**
     * Send record_video action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendRecordVideoAction(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'record_video',
        ]);
    }

    /**
     * Send record_voice action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendRecordVoiceAction(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'record_voice',
        ]);
    }

    /**
     * Send upload_voice action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendUploadVoiceAction(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'upload_voice',
        ]);
    }

    /**
     * Send choose_sticker action to a chat.
     *
     * @param int|string $chatId Target chat
     */
    public function sendChooseStickerAction(int|string $chatId): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => 'choose_sticker',
        ]);
    }

    /**
     * Send any chat action.
     *
     * @param int|string $chatId Target chat
     * @param string $action Action type
     */
    public function sendChatAction(int|string $chatId, string $action): array
    {
        return $this->callMethod('sendChatAction', [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
    }

    // =============================================
    // Moderation Methods
    // =============================================

    /**
     * Ban a user from a chat.
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to ban
     * @param int|null $untilDate Unix timestamp when the user will be unbanned (0 or null = forever)
     * @param bool $revokeMessages Delete all messages from the user
     */
    public function banChatMember(
        int|string $chatId,
        int $userId,
        ?int $untilDate = null,
        bool $revokeMessages = false
    ): array {
        $params = [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ];

        if ($untilDate !== null && $untilDate > 0) {
            $params['until_date'] = $untilDate;
        }

        if ($revokeMessages) {
            $params['revoke_messages'] = true;
        }

        return $this->callMethod('banChatMember', $params);
    }

    /**
     * Unban a user from a chat.
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to unban
     * @param bool $onlyIfBanned Only unban if the user is currently banned
     */
    public function unbanChatMember(int|string $chatId, int $userId, bool $onlyIfBanned = true): array
    {
        return $this->callMethod('unbanChatMember', [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'only_if_banned' => $onlyIfBanned,
        ]);
    }

    /**
     * Restrict a user in a chat.
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to restrict
     * @param array $permissions ChatPermissions array
     * @param int|null $untilDate Unix timestamp when restrictions will be lifted
     */
    public function restrictChatMember(
        int|string $chatId,
        int $userId,
        array $permissions,
        ?int $untilDate = null
    ): array {
        $params = [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'permissions' => json_encode($permissions),
        ];

        if ($untilDate !== null && $untilDate > 0) {
            $params['until_date'] = $untilDate;
        }

        return $this->callMethod('restrictChatMember', $params);
    }

    /**
     * Promote a user to admin.
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to promote
     * @param array $rights Admin rights to grant
     */
    public function promoteChatMember(int|string $chatId, int $userId, array $rights = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ], $rights);

        return $this->callMethod('promoteChatMember', $params);
    }

    /**
     * Mute a user (remove send message permission).
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to mute
     * @param int|null $untilDate Unix timestamp when mute will be lifted
     */
    public function muteUser(int|string $chatId, int $userId, ?int $untilDate = null): array
    {
        return $this->restrictChatMember($chatId, $userId, [
            'can_send_messages' => false,
            'can_send_audios' => false,
            'can_send_documents' => false,
            'can_send_photos' => false,
            'can_send_videos' => false,
            'can_send_video_notes' => false,
            'can_send_voice_notes' => false,
            'can_send_polls' => false,
            'can_send_other_messages' => false,
        ], $untilDate);
    }

    /**
     * Unmute a user (restore default permissions).
     *
     * @param int|string $chatId Target chat
     * @param int $userId User to unmute
     */
    public function unmuteUser(int|string $chatId, int $userId): array
    {
        return $this->restrictChatMember($chatId, $userId, [
            'can_send_messages' => true,
            'can_send_audios' => true,
            'can_send_documents' => true,
            'can_send_photos' => true,
            'can_send_videos' => true,
            'can_send_video_notes' => true,
            'can_send_voice_notes' => true,
            'can_send_polls' => true,
            'can_send_other_messages' => true,
            'can_add_web_page_previews' => true,
            'can_change_info' => false,
            'can_invite_users' => true,
            'can_pin_messages' => false,
        ]);
    }

    /**
     * Delete a message.
     *
     * @param int|string $chatId Target chat
     * @param int $messageId Message ID to delete
     */
    public function deleteMessage(int|string $chatId, int $messageId): array
    {
        return $this->callMethod('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }

    /**
     * Delete multiple messages.
     *
     * @param int|string $chatId Target chat
     * @param array $messageIds Array of message IDs to delete
     */
    public function deleteMessages(int|string $chatId, array $messageIds): array
    {
        return $this->callMethod('deleteMessages', [
            'chat_id' => $chatId,
            'message_ids' => json_encode($messageIds),
        ]);
    }

    /**
     * Pin a message.
     *
     * @param int|string $chatId Target chat
     * @param int $messageId Message ID to pin
     * @param bool $disableNotification Disable notification for members
     */
    public function pinChatMessage(int|string $chatId, int $messageId, bool $disableNotification = false): array
    {
        return $this->callMethod('pinChatMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'disable_notification' => $disableNotification,
        ]);
    }

    /**
     * Unpin a message.
     *
     * @param int|string $chatId Target chat
     * @param int|null $messageId Message ID to unpin (null = unpin most recent)
     */
    public function unpinChatMessage(int|string $chatId, ?int $messageId = null): array
    {
        $params = ['chat_id' => $chatId];
        
        if ($messageId !== null) {
            $params['message_id'] = $messageId;
        }

        return $this->callMethod('unpinChatMessage', $params);
    }

    /**
     * Unpin all messages.
     *
     * @param int|string $chatId Target chat
     */
    public function unpinAllChatMessages(int|string $chatId): array
    {
        return $this->callMethod('unpinAllChatMessages', [
            'chat_id' => $chatId,
        ]);
    }

    /**
     * Get file download URL from file_id.
     *
     * @param string $fileId File identifier to get info about
     * @return string|null Download URL or null if not found
     */
    public function getFileUrl(string $fileId): ?string
    {
        try {
            $file = $this->callMethod('getFile', ['file_id' => $fileId]);
            $filePath = $file['result']['file_path'] ?? null;

            if ($filePath === null) {
                return null;
            }

            preg_match('/bot([^\/]+)\//', $this->baseUrl, $matches);
            $token = $matches[1] ?? '';

            return "https://api.telegram.org/file/bot{$token}/{$filePath}";
        } catch (ApiException $e) {
            return null;
        }
    }

    /**
     * Download file content from file_id.
     *
     * @param string $fileId File identifier
     * @return string|null File content or null if error
     */
    public function downloadFile(string $fileId): ?string
    {
        $url = $this->getFileUrl($fileId);
        if ($url === null) {
            return null;
        }

        $content = @file_get_contents($url);
        return $content !== false ? $content : null;
    }

    // =============================================
    // Keyboard Builders
    // =============================================

    /**
     * Build an inline keyboard markup array.
     *
     * @param array $rows Array of rows, each row is an array of buttons
     * @return array InlineKeyboardMarkup array ready for json_encode
     *
     * Example:
     * $keyboard = $bot->inlineKeyboard([
     *     [
     *         ['text' => 'Button 1', 'callback_data' => 'btn1'],
     *         ['text' => 'Button 2', 'callback_data' => 'btn2'],
     *     ],
     *     [
     *         ['text' => 'Visit Website', 'url' => 'https://example.com'],
     *     ],
     * ]);
     */
    public function inlineKeyboard(array $rows): array
    {
        return ['inline_keyboard' => $rows];
    }

    /**
     * Build a reply keyboard markup array.
     *
     * @param array $rows Array of rows, each row is an array of buttons
     * @param bool $resizeKeyboard Requests clients to resize the keyboard vertically
     * @param bool $oneTimeKeyboard Requests clients to hide the keyboard after use
     * @param bool $selective Use this parameter if you want to show the keyboard to specific users only
     */
    public function replyKeyboard(array $rows, bool $resizeKeyboard = true, bool $oneTimeKeyboard = false, bool $selective = false): array
    {
        return [
            'keyboard' => $rows,
            'resize_keyboard' => $resizeKeyboard,
            'one_time_keyboard' => $oneTimeKeyboard,
            'selective' => $selective,
        ];
    }

    /**
     * Get remove keyboard markup.
     *
     * @param bool $selective Use this parameter if you want to remove the keyboard for specific users only
     */
    public function removeKeyboard(bool $selective = false): array
    {
        return [
            'remove_keyboard' => true,
            'selective' => $selective,
        ];
    }

    /**
     * Get force reply markup.
     *
     * @param bool $selective Use this parameter if you want to force reply from specific users only
     * @param string|null $inputFieldPlaceholder Placeholder text in the input field when the reply is active
     */
    public function forceReply(bool $selective = false, ?string $inputFieldPlaceholder = null): array
    {
        $markup = [
            'force_reply' => true,
            'selective' => $selective,
        ];

        if ($inputFieldPlaceholder !== null) {
            $markup['input_field_placeholder'] = $inputFieldPlaceholder;
        }

        return $markup;
    }

    // =============================================
    // Parse Mode Helpers
    // =============================================

    /**
     * Escape text for MarkdownV2 parse mode.
     *
     * @param string $text Text to escape
     */
    public function escapeMarkdownV2(string $text): string
    {
        $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        foreach ($specialChars as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }
        return $text;
    }

    /**
     * Escape text for HTML parse mode.
     *
     * @param string $text Text to escape
     */
    public function escapeHtml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // =============================================
    // Bot Commands Helper
    // =============================================

    /**
     * Set bot commands with simple array format.
     *
     * @param array $commands Associative array of command => description
     * @param array $options Optional parameters (scope, language_code)
     *
     * Example:
     * $bot->setCommands([
     *     'start' => 'Start the bot',
     *     'help' => 'Show help message',
     *     'settings' => 'Open settings',
     * ]);
     */
    public function setCommands(array $commands, array $options = []): array
    {
        $botCommands = [];
        foreach ($commands as $command => $description) {
            $botCommands[] = [
                'command' => $command,
                'description' => $description,
            ];
        }

        return $this->callMethod('setMyCommands', array_merge([
            'commands' => json_encode($botCommands),
        ], $options));
    }

    /**
     * Delete all bot commands.
     *
     * @param array $options Optional parameters (scope, language_code)
     */
    public function deleteCommands(array $options = []): array
    {
        return $this->callMethod('deleteMyCommands', $options);
    }

    /**
     * Get current bot commands as simple array.
     *
     * @param array $options Optional parameters (scope, language_code)
     * @return array Associative array of command => description
     */
    public function getCommands(array $options = []): array
    {
        $result = $this->callMethod('getMyCommands', $options);
        $commands = [];
        foreach ($result['result'] ?? [] as $cmd) {
            $commands[$cmd['command']] = $cmd['description'];
        }
        return $commands;
    }
}
