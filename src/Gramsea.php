<?php

namespace Andisiahaan\GramseaTelegramBot;

class Gramsea
{
    protected $baseUrl;

    public function __construct(string $botToken)
    {
        $this->baseUrl = "https://api.telegram.org/bot{$botToken}/";
    }
    
    public function getMe(): bool
    {
        $response = Curl::request($this->baseUrl . 'getMe');
        return isset($response['ok']) && $response['ok'] === true;
    }
    
    public function isChatMember($userId, $chatId): bool
    {
        $parameters = [
            'chat_id' => $chatId,
            'user_id' => $userId
        ];
        
        $response = Curl::request($this->baseUrl . 'getChatMember', $parameters);
        
        if (isset($response['ok']) && $response['ok'] === true) {
            $status = $response['result']['status'];
            
            // Status yang menunjukkan pengguna mengikuti channel:
            // 'creator', 'administrator', 'member', 'restricted' (jika can_send_messages=true)
            if (in_array($status, ['creator', 'administrator', 'member']) || 
                ($status == 'restricted' && isset($response['result']['can_send_messages']) && 
                 $response['result']['can_send_messages'])) {
                return true;
            }
        }
        
        return false;
    } 

    public function sendMessage(string $chat_id, string $text, string $reply_markup = ""): array
    {
        $parameters = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if (!empty($reply_markup)) {
            $parameters['reply_markup'] = $reply_markup;
        }

        return Curl::request($this->baseUrl . 'sendMessage', $parameters);
    }

    public function sendPhoto(string $chat_id, string $photo, ?string $caption = null, string $reply_markup = ""): array
    {
        $parameters = [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ];

        if (!empty($reply_markup)) {
            $parameters['reply_markup'] = $reply_markup;
        }

        return Curl::request($this->baseUrl . 'sendPhoto', $parameters);
    }

    public function sendAudio(string $chat_id, string $audio, ?string $caption = null, ?string $title = null, ?string $performer = null, string $reply_markup = ""): array
    {
        $parameters = [
            'chat_id' => $chat_id,
            'audio' => $audio,
            'caption' => $caption,
            'parse_mode' => 'HTML',
            'title' => $title,
            'performer' => $performer,
        ];

        if (!empty($reply_markup)) {
            $parameters['reply_markup'] = $reply_markup;
        }

        return Curl::request($this->baseUrl . 'sendAudio', $parameters);
    }

    public function sendVideo(string $chat_id, string $video, ?string $caption = null, string $reply_markup = ""): array
    {
        $parameters = [
            'chat_id' => $chat_id,
            'video' => $video,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ];

        if (!empty($reply_markup)) {
            $parameters['reply_markup'] = $reply_markup;
        }

        return Curl::request($this->baseUrl . 'sendVideo', $parameters);
    }

    public function sendDocument(string $chat_id, string $document, ?string $caption = null, string $reply_markup = ""): array
    {
        $parameters = [
            'chat_id' => $chat_id,
            'document' => $document,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ];

        if (!empty($reply_markup)) {
            $parameters['reply_markup'] = $reply_markup;
        }

        return Curl::request($this->baseUrl . 'sendDocument', $parameters);
    }

    public function sendMediaGroup(string $chat_id, array $media): array
    {
        $parameters = [
            'chat_id' => $chat_id,
            'media' => json_encode($media) ?? $media,
        ];

        return Curl::request($this->baseUrl . 'sendMediaGroup', $parameters);
    }

    public function setWebhook(string $url, array $options = []): array
    {
        $parameters = array_merge(['url' => $url], $options);
        return Curl::request($this->baseUrl . 'setWebhook', $parameters);
    }

    public function deleteWebhook(): array
    {
        return Curl::request($this->baseUrl . 'deleteWebhook');
    }

    public function deleteMessage(string $chat_id, int $message_id): array
    {
        $parameters = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ];

        return Curl::request($this->baseUrl . 'deleteMessage', $parameters);
    }
}
