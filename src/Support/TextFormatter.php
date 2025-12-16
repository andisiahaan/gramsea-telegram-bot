<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Fluent builder untuk formatting text Telegram (HTML mode).
 * 
 * Example:
 * $text = TextFormatter::make()
 *     ->bold('Welcome!')
 *     ->newLine()
 *     ->text('Hello, ')
 *     ->mention('John', 123456789)
 *     ->toString();
 */
class TextFormatter
{
    protected string $content = '';

    /**
     * Create new TextFormatter instance.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Add plain text.
     */
    public function text(string $text): self
    {
        $this->content .= self::escape($text);
        return $this;
    }

    /**
     * Add raw HTML (tanpa escape).
     */
    public function raw(string $html): self
    {
        $this->content .= $html;
        return $this;
    }

    /**
     * Add bold text.
     */
    public function bold(string $text): self
    {
        $this->content .= '<b>' . self::escape($text) . '</b>';
        return $this;
    }

    /**
     * Add italic text.
     */
    public function italic(string $text): self
    {
        $this->content .= '<i>' . self::escape($text) . '</i>';
        return $this;
    }

    /**
     * Add underline text.
     */
    public function underline(string $text): self
    {
        $this->content .= '<u>' . self::escape($text) . '</u>';
        return $this;
    }

    /**
     * Add strikethrough text.
     */
    public function strike(string $text): self
    {
        $this->content .= '<s>' . self::escape($text) . '</s>';
        return $this;
    }

    /**
     * Add spoiler text.
     */
    public function spoiler(string $text): self
    {
        $this->content .= '<tg-spoiler>' . self::escape($text) . '</tg-spoiler>';
        return $this;
    }

    /**
     * Add inline code.
     */
    public function code(string $text): self
    {
        $this->content .= '<code>' . self::escape($text) . '</code>';
        return $this;
    }

    /**
     * Add code block dengan language.
     */
    public function codeBlock(string $code, ?string $language = null): self
    {
        if ($language !== null) {
            $this->content .= '<pre><code class="language-' . self::escape($language) . '">' . self::escape($code) . '</code></pre>';
        } else {
            $this->content .= '<pre>' . self::escape($code) . '</pre>';
        }
        return $this;
    }

    /**
     * Add link.
     */
    public function link(string $text, string $url): self
    {
        $this->content .= '<a href="' . self::escape($url) . '">' . self::escape($text) . '</a>';
        return $this;
    }

    /**
     * Add text mention (link ke user).
     */
    public function mention(string $text, int $userId): self
    {
        $this->content .= '<a href="tg://user?id=' . $userId . '">' . self::escape($text) . '</a>';
        return $this;
    }

    /**
     * Add @username mention.
     */
    public function usernameMention(string $username): self
    {
        $username = ltrim($username, '@');
        $this->content .= '@' . self::escape($username);
        return $this;
    }

    /**
     * Add hashtag.
     */
    public function hashtag(string $tag): self
    {
        $tag = ltrim($tag, '#');
        $this->content .= '#' . self::escape($tag);
        return $this;
    }

    /**
     * Add blockquote.
     */
    public function quote(string $text): self
    {
        $this->content .= '<blockquote>' . self::escape($text) . '</blockquote>';
        return $this;
    }

    /**
     * Add expandable blockquote.
     */
    public function expandableQuote(string $text): self
    {
        $this->content .= '<blockquote expandable>' . self::escape($text) . '</blockquote>';
        return $this;
    }

    /**
     * Add new line.
     */
    public function newLine(int $count = 1): self
    {
        $this->content .= str_repeat("\n", $count);
        return $this;
    }

    /**
     * Alias untuk newLine.
     */
    public function br(int $count = 1): self
    {
        return $this->newLine($count);
    }

    /**
     * Add space.
     */
    public function space(int $count = 1): self
    {
        $this->content .= str_repeat(' ', $count);
        return $this;
    }

    /**
     * Add horizontal separator line.
     */
    public function separator(string $char = '─', int $length = 20): self
    {
        $this->content .= str_repeat($char, $length);
        return $this;
    }

    /**
     * Add emoji.
     */
    public function emoji(string $emoji): self
    {
        $this->content .= $emoji;
        return $this;
    }

    /**
     * Conditional append.
     */
    public function when(bool $condition, callable $callback): self
    {
        if ($condition) {
            $callback($this);
        }
        return $this;
    }

    /**
     * Get the formatted string.
     */
    public function toString(): string
    {
        return $this->content;
    }

    /**
     * Magic method untuk string conversion.
     */
    public function __toString(): string
    {
        return $this->content;
    }

    /**
     * Clear content.
     */
    public function clear(): self
    {
        $this->content = '';
        return $this;
    }

    /**
     * Get current length.
     */
    public function length(): int
    {
        return mb_strlen($this->content);
    }

    // =============================================
    // Static Helper Methods
    // =============================================

    /**
     * Escape text untuk HTML parse mode.
     */
    public static function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Static shortcut untuk bold.
     */
    public static function b(string $text): string
    {
        return '<b>' . self::escape($text) . '</b>';
    }

    /**
     * Static shortcut untuk italic.
     */
    public static function i(string $text): string
    {
        return '<i>' . self::escape($text) . '</i>';
    }

    /**
     * Static shortcut untuk code.
     */
    public static function c(string $text): string
    {
        return '<code>' . self::escape($text) . '</code>';
    }

    /**
     * Static shortcut untuk link.
     */
    public static function a(string $text, string $url): string
    {
        return '<a href="' . self::escape($url) . '">' . self::escape($text) . '</a>';
    }

    /**
     * Static shortcut untuk user mention.
     */
    public static function user(string $name, int $userId): string
    {
        return '<a href="tg://user?id=' . $userId . '">' . self::escape($name) . '</a>';
    }

    // =============================================
    // Markdown to HTML Conversion
    // =============================================

    /**
     * Convert markdown syntax ke HTML.
     * 
     * Mendukung:
     * - **bold** atau __bold__
     * - *italic* atau _italic_
     * - `code`
     * - ~~strikethrough~~
     * - [text](url)
     */
    public static function markdownToHtml(string $text): string
    {
        if (!self::hasMarkdownSyntax($text)) {
            return $text;
        }

        $conversions = [
            // Bold: **text** or __text__ -> <b>text</b>
            '/\*\*(.*?)\*\*/' => '<b>$1</b>',
            '/__(.*?)__/' => '<b>$1</b>',
            
            // Italic: *text* or _text_ -> <i>text</i>
            '/\*(.*?)\*/' => '<i>$1</i>',
            '/_(.*?)_/' => '<i>$1</i>',
            
            // Code: `text` -> <code>text</code>
            '/`(.*?)`/' => '<code>$1</code>',
            
            // Strikethrough: ~~text~~ -> <s>text</s>
            '/~~(.*?)~~/' => '<s>$1</s>',
            
            // Links: [text](url) -> <a href="url">text</a>
            '/\[([^\]]+)\]\(([^)]+)\)/' => '<a href="$2">$1</a>',
        ];

        foreach ($conversions as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }

    /**
     * Check apakah text mengandung markdown syntax.
     */
    public static function hasMarkdownSyntax(string $text): bool
    {
        $patterns = [
            '/\*\*.*?\*\*/',     // Bold **text**
            '/__.*?__/',         // Bold __text__
            '/\*.*?\*/',         // Italic *text*
            '/_.*?_/',           // Italic _text_
            '/`.*?`/',           // Code `text`
            '/~~.*?~~/',         // Strikethrough ~~text~~
            '/\[.*?\]\(.*?\)/',  // Links [text](url)
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
