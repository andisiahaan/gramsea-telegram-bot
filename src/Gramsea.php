<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Exception\BadRequestException;
use AndiSiahaan\GramseaTelegramBot\Exception\UnauthorizedException;
use AndiSiahaan\GramseaTelegramBot\Exception\ForbiddenException;
use AndiSiahaan\GramseaTelegramBot\Exception\NotFoundException;
use AndiSiahaan\GramseaTelegramBot\Exception\ConflictException;
use AndiSiahaan\GramseaTelegramBot\Exception\TooManyRequestsException;
use AndiSiahaan\GramseaTelegramBot\Exception\TelegramServerException;

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
        $errorCode = $response['error_code'] ?? ($response['http_code'] ?? 0);

        throw $this->createException($message, $response, $errorCode);
    }

    /**
     * Create specific exception based on error code.
     */
    protected function createException(string $message, array $response, int $errorCode): ApiException
    {
        return match (true) {
            $errorCode === 400 => new BadRequestException($message, $response, $errorCode),
            $errorCode === 401 => new UnauthorizedException($message, $response, $errorCode),
            $errorCode === 403 => new ForbiddenException($message, $response, $errorCode),
            $errorCode === 404 => new NotFoundException($message, $response, $errorCode),
            $errorCode === 409 => new ConflictException($message, $response, $errorCode),
            $errorCode === 429 => new TooManyRequestsException($message, $response, $errorCode),
            $errorCode >= 500 => new TelegramServerException($message, $response, $errorCode),
            default => new ApiException($message, $response, $errorCode),
        };
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

    /**
     * Create MassMessageSender instance untuk concurrent mass sending.
     * 
     * @example
     * $bot->mass()
     *     ->addTarget(['chat_id' => '123', 'text' => 'Hello!'])
     *     ->addTarget(['chat_id' => '456', 'text' => 'Hi!'])
     *     ->send();
     */
    public function mass(): MassMessageSender
    {
        return MassMessageSender::make($this);
    }
    
    
    /**
     * Alias untuk mass().
     * 
     * @example
     * $bot->massMessage()
     *     ->addTarget(['chat_id' => '123', 'text' => 'Hello!'])
     *     ->addTarget(['chat_id' => '456', 'text' => 'Hi!'])
     *     ->send();
     */
    public function massMessage(): MassMessageSender
    {
        return MassMessageSender::make($this);
    }
}
