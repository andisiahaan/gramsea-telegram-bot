<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Helper class untuk memformat reply markup keyboard.
 */
class ReplyMarkup
{
    /**
     * Format content menjadi reply markup JSON.
     * 
     * @param array|string $content Array atau JSON string dari buttons
     * @param int $maxCharsPerLine Maximum karakter per baris
     * @return string|null JSON string reply markup atau null jika gagal
     */
    public static function format(array|string $content, int $maxCharsPerLine = 40): ?string
    {   
        if (!is_array($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content = $decoded;
            } else {
                return null;
            }
        }

        if (empty($content)) {
            return null;
        }

        $type = array_is_list($content) ? 'reply' : 'inline';
        
        $buttons = [];
        $currentRow = [];
        $currentRowLength = 0;

        $maxCharsPerLine = ($type === 'inline') ? (int)($maxCharsPerLine * 0.8) : $maxCharsPerLine;

        foreach ($content as $text => $callbackData) {
            if ($type === 'reply') {
                $text = $callbackData;
                $callbackData = null;
            }

            $textLength = mb_strlen($text, 'UTF-8');
            
            // Jika tombol ini sendiri sudah melebihi batas
            if ($textLength > $maxCharsPerLine) {
                // Jika ada tombol dalam baris saat ini, tambahkan ke hasil
                if (!empty($currentRow)) {
                    $buttons[] = $currentRow;
                    $currentRow = [];
                    $currentRowLength = 0;
                }
                
                // Tambahkan tombol panjang ini sebagai baris sendiri (tanpa dipotong)
                if ($type === 'inline') {
                    $callbackData = str_replace("\n", '%0A', $callbackData);
                    $buttons[] = [['text' => $text, 'url' => $callbackData]];
                } elseif ($type === 'reply') {
                    $buttons[] = [['text' => $text]];
                }
                
                continue;
            }
            
            // Jika menambahkan tombol ini akan melebihi batas, buat baris baru
            if ($currentRowLength + $textLength > $maxCharsPerLine && !empty($currentRow)) {
                $buttons[] = $currentRow;
                $currentRow = [];
                $currentRowLength = 0;
            }

            // Tambahkan tombol ke baris saat ini
            if ($type === 'inline') {
                $callbackData = str_replace("\n", '%0A', $callbackData);
                $currentRow[] = ['text' => $text, 'url' => $callbackData];
            } elseif ($type === 'reply') {
                $currentRow[] = ['text' => $text];
            }
            
            $currentRowLength += $textLength;
        }

        // Jangan lupa tambahkan baris terakhir jika ada
        if (!empty($currentRow)) {
            $buttons[] = $currentRow;
        }
        
        if ($type === 'inline') {
            return json_encode(['inline_keyboard' => $buttons]);
        } elseif ($type === 'reply') {
            return json_encode([
                'keyboard' => $buttons,
                'is_persistent' => true,
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ]);
        }
        
        return null;
    }

    /**
     * Build inline keyboard dengan callback_data.
     * 
     * @param array $buttons Associative array: text => callback_data
     * @param int $maxCharsPerLine Maximum karakter per baris
     * @return string JSON string inline keyboard
     */
    public static function inlineCallback(array $buttons, int $maxCharsPerLine = 32): string
    {
        $keyboard = [];
        $currentRow = [];
        $currentRowLength = 0;

        foreach ($buttons as $text => $callbackData) {
            $textLength = mb_strlen($text, 'UTF-8');
            
            if ($textLength > $maxCharsPerLine) {
                if (!empty($currentRow)) {
                    $keyboard[] = $currentRow;
                    $currentRow = [];
                    $currentRowLength = 0;
                }
                $keyboard[] = [['text' => $text, 'callback_data' => $callbackData]];
                continue;
            }
            
            if ($currentRowLength + $textLength > $maxCharsPerLine && !empty($currentRow)) {
                $keyboard[] = $currentRow;
                $currentRow = [];
                $currentRowLength = 0;
            }

            $currentRow[] = ['text' => $text, 'callback_data' => $callbackData];
            $currentRowLength += $textLength;
        }

        if (!empty($currentRow)) {
            $keyboard[] = $currentRow;
        }

        return json_encode(['inline_keyboard' => $keyboard]);
    }

    /**
     * Build inline keyboard dengan URL.
     * 
     * @param array $buttons Associative array: text => url
     * @param int $maxCharsPerLine Maximum karakter per baris
     * @return string JSON string inline keyboard
     */
    public static function inlineUrl(array $buttons, int $maxCharsPerLine = 32): string
    {
        $keyboard = [];
        $currentRow = [];
        $currentRowLength = 0;

        foreach ($buttons as $text => $url) {
            $textLength = mb_strlen($text, 'UTF-8');
            
            if ($textLength > $maxCharsPerLine) {
                if (!empty($currentRow)) {
                    $keyboard[] = $currentRow;
                    $currentRow = [];
                    $currentRowLength = 0;
                }
                $keyboard[] = [['text' => $text, 'url' => $url]];
                continue;
            }
            
            if ($currentRowLength + $textLength > $maxCharsPerLine && !empty($currentRow)) {
                $keyboard[] = $currentRow;
                $currentRow = [];
                $currentRowLength = 0;
            }

            $currentRow[] = ['text' => $text, 'url' => $url];
            $currentRowLength += $textLength;
        }

        if (!empty($currentRow)) {
            $keyboard[] = $currentRow;
        }

        return json_encode(['inline_keyboard' => $keyboard]);
    }

    /**
     * Build reply keyboard.
     * 
     * @param array $buttons Array of button texts
     * @param int $maxCharsPerLine Maximum karakter per baris
     * @param bool $isPersistent Keyboard akan tetap tampil
     * @param bool $resizeKeyboard Resize keyboard sesuai ukuran tombol
     * @param bool $oneTimeKeyboard Keyboard hilang setelah digunakan
     * @return string JSON string reply keyboard
     */
    public static function reply(
        array $buttons, 
        int $maxCharsPerLine = 40,
        bool $isPersistent = true,
        bool $resizeKeyboard = true,
        bool $oneTimeKeyboard = false
    ): string {
        $keyboard = [];
        $currentRow = [];
        $currentRowLength = 0;

        foreach ($buttons as $text) {
            $textLength = mb_strlen($text, 'UTF-8');
            
            if ($textLength > $maxCharsPerLine) {
                if (!empty($currentRow)) {
                    $keyboard[] = $currentRow;
                    $currentRow = [];
                    $currentRowLength = 0;
                }
                $keyboard[] = [['text' => $text]];
                continue;
            }
            
            if ($currentRowLength + $textLength > $maxCharsPerLine && !empty($currentRow)) {
                $keyboard[] = $currentRow;
                $currentRow = [];
                $currentRowLength = 0;
            }

            $currentRow[] = ['text' => $text];
            $currentRowLength += $textLength;
        }

        if (!empty($currentRow)) {
            $keyboard[] = $currentRow;
        }

        return json_encode([
            'keyboard' => $keyboard,
            'is_persistent' => $isPersistent,
            'resize_keyboard' => $resizeKeyboard,
            'one_time_keyboard' => $oneTimeKeyboard
        ]);
    }

    /**
     * Build remove keyboard markup.
     * 
     * @param bool $selective Hanya hapus untuk user tertentu
     * @return string JSON string remove keyboard
     */
    public static function remove(bool $selective = false): string
    {
        return json_encode([
            'remove_keyboard' => true,
            'selective' => $selective
        ]);
    }

    /**
     * Build force reply markup.
     * 
     * @param bool $selective Hanya force reply untuk user tertentu
     * @param string|null $placeholder Placeholder text di input field
     * @return string JSON string force reply
     */
    public static function forceReply(bool $selective = false, ?string $placeholder = null): string
    {
        $markup = [
            'force_reply' => true,
            'selective' => $selective
        ];

        if ($placeholder !== null) {
            $markup['input_field_placeholder'] = $placeholder;
        }

        return json_encode($markup);
    }
}
