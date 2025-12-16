<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Helper class untuk membangun inline keyboard dengan berbagai pattern.
 * 
 * Extends fitur dari ReplyMarkup dengan pattern-pattern umum.
 */
class InlineKeyboard
{
    protected array $rows = [];
    protected array $currentRow = [];

    /**
     * Create new instance.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Add button to current row.
     */
    public function button(string $text, array $options): self
    {
        $button = ['text' => $text];
        
        if (isset($options['url'])) {
            $button['url'] = $options['url'];
        } elseif (isset($options['callback_data'])) {
            $button['callback_data'] = $options['callback_data'];
        } elseif (isset($options['callback'])) {
            $button['callback_data'] = $options['callback'];
        } elseif (isset($options['web_app'])) {
            $button['web_app'] = ['url' => $options['web_app']];
        } elseif (isset($options['login_url'])) {
            $button['login_url'] = ['url' => $options['login_url']];
        } elseif (isset($options['switch_inline_query'])) {
            $button['switch_inline_query'] = $options['switch_inline_query'];
        } elseif (isset($options['switch_inline_query_current_chat'])) {
            $button['switch_inline_query_current_chat'] = $options['switch_inline_query_current_chat'];
        } elseif (isset($options['copy_text'])) {
            $button['copy_text'] = ['text' => $options['copy_text']];
        }

        $this->currentRow[] = $button;
        return $this;
    }

    /**
     * Add callback button.
     */
    public function callback(string $text, string $data): self
    {
        return $this->button($text, ['callback_data' => $data]);
    }

    /**
     * Add URL button.
     */
    public function url(string $text, string $url): self
    {
        return $this->button($text, ['url' => $url]);
    }

    /**
     * Add Web App button.
     */
    public function webApp(string $text, string $url): self
    {
        return $this->button($text, ['web_app' => $url]);
    }

    /**
     * Add login URL button.
     */
    public function loginUrl(string $text, string $url): self
    {
        return $this->button($text, ['login_url' => $url]);
    }

    /**
     * Add switch inline query button.
     */
    public function switchInline(string $text, string $query = ''): self
    {
        return $this->button($text, ['switch_inline_query' => $query]);
    }

    /**
     * Add switch inline query current chat button.
     */
    public function switchInlineCurrentChat(string $text, string $query = ''): self
    {
        return $this->button($text, ['switch_inline_query_current_chat' => $query]);
    }

    /**
     * Add copy text button.
     */
    public function copyText(string $text, string $textToCopy): self
    {
        return $this->button($text, ['copy_text' => $textToCopy]);
    }

    /**
     * End current row and start a new one.
     */
    public function row(): self
    {
        if (!empty($this->currentRow)) {
            $this->rows[] = $this->currentRow;
            $this->currentRow = [];
        }
        return $this;
    }

    /**
     * Add pagination buttons.
     * 
     * @param int $currentPage Current page number
     * @param int $totalPages Total number of pages
     * @param string $callbackPrefix Prefix for callback data (e.g., 'page_')
     */
    public function pagination(int $currentPage, int $totalPages, string $callbackPrefix = 'page_'): self
    {
        $this->row();
        
        if ($currentPage > 1) {
            $this->callback('« First', $callbackPrefix . '1');
            $this->callback('‹ Prev', $callbackPrefix . ($currentPage - 1));
        }
        
        $this->callback("$currentPage / $totalPages", $callbackPrefix . 'current');
        
        if ($currentPage < $totalPages) {
            $this->callback('Next ›', $callbackPrefix . ($currentPage + 1));
            $this->callback('Last »', $callbackPrefix . $totalPages);
        }
        
        return $this->row();
    }

    /**
     * Add simple prev/next pagination.
     */
    public function simplePagination(int $currentPage, int $totalPages, string $callbackPrefix = 'page_'): self
    {
        $this->row();
        
        if ($currentPage > 1) {
            $this->callback('‹ Prev', $callbackPrefix . ($currentPage - 1));
        }
        
        if ($currentPage < $totalPages) {
            $this->callback('Next ›', $callbackPrefix . ($currentPage + 1));
        }
        
        return $this->row();
    }

    /**
     * Add confirmation buttons (Yes/No).
     */
    public function confirm(string $yesCallback, string $noCallback, string $yesText = '✅ Yes', string $noText = '❌ No'): self
    {
        return $this->row()
            ->callback($yesText, $yesCallback)
            ->callback($noText, $noCallback)
            ->row();
    }

    /**
     * Add back button.
     */
    public function back(string $callback, string $text = '« Back'): self
    {
        return $this->row()->callback($text, $callback)->row();
    }

    /**
     * Add close button.
     */
    public function close(string $callback = 'close', string $text = '✖ Close'): self
    {
        return $this->row()->callback($text, $callback)->row();
    }

    /**
     * Add menu grid from array.
     * 
     * @param array $items Array of ['text' => ..., 'callback' => ...] or ['text' => ..., 'url' => ...]
     * @param int $columns Number of columns per row
     */
    public function grid(array $items, int $columns = 2): self
    {
        $this->row();
        
        $count = 0;
        foreach ($items as $item) {
            if (isset($item['callback'])) {
                $this->callback($item['text'], $item['callback']);
            } elseif (isset($item['url'])) {
                $this->url($item['text'], $item['url']);
            }
            
            $count++;
            if ($count % $columns === 0) {
                $this->row();
            }
        }
        
        return $this->row();
    }

    /**
     * Add numbered list buttons.
     * 
     * @param array $items Array of items
     * @param string $callbackPrefix Prefix for callback data
     * @param int $offset Starting number
     */
    public function numberedList(array $items, string $callbackPrefix, int $offset = 0): self
    {
        $this->row();
        
        foreach ($items as $index => $item) {
            $num = $index + 1 + $offset;
            $text = is_array($item) ? $item['text'] : $item;
            $this->callback("{$num}. {$text}", $callbackPrefix . ($index + $offset));
            $this->row();
        }
        
        return $this;
    }

    /**
     * Build and return as array.
     */
    public function toArray(): array
    {
        // Make sure to include current row if not empty
        $rows = $this->rows;
        if (!empty($this->currentRow)) {
            $rows[] = $this->currentRow;
        }
        
        return ['inline_keyboard' => $rows];
    }

    /**
     * Build and return as JSON string.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Magic method for string conversion.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Reset the keyboard.
     */
    public function clear(): self
    {
        $this->rows = [];
        $this->currentRow = [];
        return $this;
    }

    // =============================================
    // Static Factory Methods
    // =============================================

    /**
     * Create simple Yes/No confirmation.
     */
    public static function yesNo(string $yesCallback, string $noCallback): array
    {
        return self::make()
            ->callback('✅ Yes', $yesCallback)
            ->callback('❌ No', $noCallback)
            ->toArray();
    }

    /**
     * Create single URL button.
     */
    public static function singleUrl(string $text, string $url): array
    {
        return self::make()->url($text, $url)->toArray();
    }

    /**
     * Create single callback button.
     */
    public static function singleCallback(string $text, string $callback): array
    {
        return self::make()->callback($text, $callback)->toArray();
    }
}
