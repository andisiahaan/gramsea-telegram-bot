<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Support\MassSendResult;
use AndiSiahaan\GramseaTelegramBot\Support\MediaHelper;
use AndiSiahaan\GramseaTelegramBot\Support\TextFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * Mass message sender untuk concurrent sending ke multiple targets.
 * 
 * Setiap target memiliki konten sendiri (text, media, reply_markup).
 * Menggunakan Guzzle Pool untuk concurrent HTTP requests.
 * 
 * @example
 * $bot->mass()
 *     ->addTarget(['chat_id' => '123', 'text' => 'Hello John!'])
 *     ->addTarget(['chat_id' => '456', 'text' => 'Hi Jane!', 'media' => ['img.jpg']])
 *     ->send();
 */
class MassMessageSender
{
    protected Gramsea $bot;
    protected string $baseUrl;
    protected array $targets = [];
    
    // Global options
    protected string $parseMode = 'HTML';
    protected bool $disableNotification = false;
    protected bool $protectContent = false;
    protected bool $allowPaidBroadcast = false;
    protected int $concurrency = 30;

    public function __construct(Gramsea $bot)
    {
        $this->bot = $bot;
        
        // Extract baseUrl from Gramsea instance
        $reflection = new \ReflectionClass($bot);
        $property = $reflection->getProperty('baseUrl');
        $property->setAccessible(true);
        $this->baseUrl = $property->getValue($bot);
    }

    /**
     * Create new instance from Gramsea bot.
     */
    public static function make(Gramsea $bot): static
    {
        return new static($bot);
    }

    /**
     * Add single target.
     * 
     * @param array $target Keys: chat_id (required), text, media, reply_markup
     */
    public function addTarget(array $target): static
    {
        if (empty($target['chat_id'])) {
            throw new \InvalidArgumentException('Target must have chat_id.');
        }
        $this->targets[] = $target;
        return $this;
    }

    /**
     * Add multiple targets.
     * 
     * @param array $targets Array of target configs
     */
    public function addTargets(array $targets): static
    {
        foreach ($targets as $target) {
            $this->addTarget($target);
        }
        return $this;
    }

    /**
     * Set parse mode (HTML, Markdown, MarkdownV2).
     */
    public function parseMode(string $mode): static
    {
        $this->parseMode = $mode;
        return $this;
    }

    /**
     * Enable silent mode (disable notification).
     */
    public function silent(bool $silent = true): static
    {
        $this->disableNotification = $silent;
        return $this;
    }

    /**
     * Protect content from forwarding/saving.
     */
    public function protect(bool $protect = true): static
    {
        $this->protectContent = $protect;
        return $this;
    }

    /**
     * Allow paid broadcast.
     */
    public function allowPaidBroadcast(bool $allow = true): static
    {
        $this->allowPaidBroadcast = $allow;
        return $this;
    }

    /**
     * Set concurrency limit.
     */
    public function concurrency(int $limit): static
    {
        $this->concurrency = max(1, $limit);
        return $this;
    }

    /**
     * Execute concurrent sending.
     */
    public function send(): MassSendResult
    {
        if (empty($this->targets)) {
            throw new \InvalidArgumentException('No targets added. Use ->addTarget() or ->addTargets().');
        }

        $result = new MassSendResult();
        $client = new Client(['timeout' => 30]);
        
        // Build requests
        $requests = function () {
            foreach ($this->targets as $index => $target) {
                yield $index => $this->buildRequest($target);
            }
        };

        $pool = new Pool($client, $requests(), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function (ResponseInterface $response, $index) use ($result) {
                $chatId = (string) $this->targets[$index]['chat_id'];
                $body = json_decode($response->getBody()->getContents(), true);
                
                if (isset($body['ok']) && $body['ok'] === true) {
                    $result->addSuccess($chatId);
                } else {
                    $errorCode = $body['error_code'] ?? 0;
                    if (in_array($errorCode, [400, 403])) {
                        $result->addBlocked($chatId);
                    } else {
                        $result->addFailed($chatId);
                    }
                }
            },
            'rejected' => function (RequestException $reason, $index) use ($result) {
                $chatId = (string) $this->targets[$index]['chat_id'];
                
                $response = $reason->getResponse();
                if ($response) {
                    $statusCode = $response->getStatusCode();
                    if (in_array($statusCode, [400, 403])) {
                        $result->addBlocked($chatId);
                        return;
                    }
                }
                
                $result->addFailed($chatId);
            },
        ]);

        // Execute pool
        $promise = $pool->promise();
        $promise->wait();

        return $result;
    }

    /**
     * Build HTTP request for a target.
     */
    protected function buildRequest(array $target): Request
    {
        $chatId = $target['chat_id'];
        $text = $target['text'] ?? null;
        $media = $target['media'] ?? [];
        $replyMarkup = $target['reply_markup'] ?? null;

        // Convert text markdown to HTML
        if ($text !== null) {
            $text = TextFormatter::markdownToHtml($text);
        }

        // Normalize media to array
        if (is_string($media)) {
            $media = [$media];
        }

        $mediaCount = count($media);

        // Determine method and params
        if ($mediaCount === 0 && $text !== null) {
            // Text only -> sendMessage
            return $this->buildTextRequest($chatId, $text, $replyMarkup);
        }

        if ($mediaCount === 1) {
            // Single media -> sendPhoto/sendVideo/etc
            return $this->buildSingleMediaRequest($chatId, $text, $media[0], $replyMarkup);
        }

        if ($mediaCount >= 2) {
            // Media group -> sendMediaGroup
            return $this->buildMediaGroupRequest($chatId, $text, $media, $replyMarkup);
        }

        // Fallback: no content, still try sendMessage
        return $this->buildTextRequest($chatId, $text ?? '', $replyMarkup);
    }

    /**
     * Build sendMessage request.
     */
    protected function buildTextRequest(string $chatId, string $text, ?string $replyMarkup): Request
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $this->parseMode,
        ];

        $this->applyGlobalOptions($params);

        if ($replyMarkup) {
            $params['reply_markup'] = $replyMarkup;
        }

        return new Request(
            'POST',
            $this->baseUrl . 'sendMessage',
            ['Content-Type' => 'application/json'],
            json_encode($params)
        );
    }

    /**
     * Build single media request.
     */
    protected function buildSingleMediaRequest(string $chatId, ?string $caption, string $mediaUrl, ?string $replyMarkup): Request
    {
        $mediaType = MediaHelper::getMediaType($mediaUrl);
        $method = 'send' . ucfirst($mediaType);

        $params = [
            'chat_id' => $chatId,
            $mediaType => $mediaUrl,
        ];

        if ($caption) {
            $params['caption'] = $caption;
            $params['parse_mode'] = $this->parseMode;
        }

        $this->applyGlobalOptions($params);

        if ($replyMarkup) {
            $params['reply_markup'] = $replyMarkup;
        }

        return new Request(
            'POST',
            $this->baseUrl . $method,
            ['Content-Type' => 'application/json'],
            json_encode($params)
        );
    }

    /**
     * Build sendMediaGroup request.
     * Note: Media groups don't support reply_markup, so we send caption separately if keyboard provided.
     */
    protected function buildMediaGroupRequest(string $chatId, ?string $caption, array $mediaUrls, ?string $replyMarkup): Request
    {
        $mediaGroup = [];
        $isFirst = true;

        foreach ($mediaUrls as $url) {
            $item = [
                'type' => MediaHelper::getMediaType($url),
                'media' => $url,
            ];

            // Add caption to first item (only if no reply markup)
            if ($isFirst && $caption && !$replyMarkup) {
                $item['caption'] = $caption;
                $item['parse_mode'] = $this->parseMode;
                $isFirst = false;
            }

            $mediaGroup[] = $item;
        }

        $params = [
            'chat_id' => $chatId,
            'media' => json_encode($mediaGroup),
        ];

        $this->applyGlobalOptions($params);

        return new Request(
            'POST',
            $this->baseUrl . 'sendMediaGroup',
            ['Content-Type' => 'application/json'],
            json_encode($params)
        );
    }

    /**
     * Apply global options to params.
     */
    protected function applyGlobalOptions(array &$params): void
    {
        if ($this->disableNotification) {
            $params['disable_notification'] = true;
        }

        if ($this->protectContent) {
            $params['protect_content'] = true;
        }

        if ($this->allowPaidBroadcast) {
            $params['allow_paid_broadcast'] = true;
        }
    }

    /**
     * Get current target count.
     */
    public function count(): int
    {
        return count($this->targets);
    }

    /**
     * Reset all targets.
     */
    public function reset(): static
    {
        $this->targets = [];
        return $this;
    }
}
