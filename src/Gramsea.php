<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;

class Gramsea
{
    use HelperMethods;

    protected string $baseUrl;

    public function __construct(string $botToken)
    {
        if (trim($botToken) === '') {
            throw new \InvalidArgumentException('Bot token cannot be empty.');
        }

        $this->baseUrl = "https://api.telegram.org/bot{$botToken}/";
    }

    public function __call(string $method, array $arguments): array
    {
        $parameters = $arguments[0] ?? [];
        return $this->callMethod($method, $parameters);
    }

    public function callMethod(string $method, array $parameters = []): array
    {
        $response = Curl::request($this->baseUrl . $method, $parameters, 'POST');

        if (isset($response['ok']) && $response['ok'] === true) {
            return $response;
        }

        $message = $response['description'] ?? 'Telegram API error';
        $httpCode = $response['http_code'] ?? 0;

        throw new ApiException($message, $response, $httpCode);
    }

    public function getMe(): array
    {
        return $this->callMethod('getMe');
    }

    // =========================================================================
    // SENDER FACTORY METHODS
    // =========================================================================

    /**
     * Create MessageSender instance untuk fluent chaining.
     * 
     * @example
     * $bot->message()->to($chatId)->text('Hello!')->send();
     */
    public function message(): MessageSender
    {
        return MessageSender::make($this);
    }

    /**
     * Alias untuk message().
     */
    public function messageSender(): MessageSender
    {
        return $this->message();
    }

    /**
     * Create TextSender instance untuk fluent chaining.
     * 
     * @example
     * $bot->text()->to($chatId)->text('Hello!')->noPreview()->send();
     */
    public function text(): TextSender
    {
        return TextSender::make($this);
    }

    /**
     * Alias untuk text().
     */
    public function textSender(): TextSender
    {
        return $this->text();
    }

    /**
     * Create MediaSender instance untuk fluent chaining.
     * 
     * @example
     * $bot->media()->to($chatId)->photo('url')->caption('Nice!')->send();
     */
    public function media(): MediaSender
    {
        return MediaSender::make($this);
    }

    /**
     * Alias untuk media().
     */
    public function mediaSender(): MediaSender
    {
        return $this->media();
    }

    /**
     * Create MediaGroupSender instance untuk fluent chaining.
     * 
     * @example
     * $bot->mediaGroup()->to($chatId)->photo('a.jpg')->photo('b.jpg')->send();
     */
    public function mediaGroup(): MediaGroupSender
    {
        return MediaGroupSender::make($this);
    }

    /**
     * Alias untuk mediaGroup().
     */
    public function mediaGroupSender(): MediaGroupSender
    {
        return $this->mediaGroup();
    }
}
