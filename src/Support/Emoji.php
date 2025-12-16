<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Constants untuk emoji yang sering digunakan di bot.
 */
class Emoji
{
    // =============================================
    // Status & Indicators
    // =============================================
    
    public const CHECK = '✅';
    public const CROSS = '❌';
    public const WARNING = '⚠️';
    public const INFO = 'ℹ️';
    public const QUESTION = '❓';
    public const EXCLAMATION = '❗';
    public const LOADING = '⏳';
    public const DONE = '✔️';
    public const PENDING = '🔄';
    public const STOP = '🛑';
    public const OK = '🆗';
    public const NEW = '🆕';
    public const FREE = '🆓';

    // =============================================
    // Arrows & Navigation
    // =============================================

    public const ARROW_LEFT = '⬅️';
    public const ARROW_RIGHT = '➡️';
    public const ARROW_UP = '⬆️';
    public const ARROW_DOWN = '⬇️';
    public const ARROW_BACK = '🔙';
    public const ARROW_FORWARD = '🔜';
    public const ARROW_TOP = '🔝';
    public const RELOAD = '🔄';
    public const NEXT = '⏭️';
    public const PREV = '⏮️';

    // =============================================
    // Numbers
    // =============================================

    public const NUM_0 = '0️⃣';
    public const NUM_1 = '1️⃣';
    public const NUM_2 = '2️⃣';
    public const NUM_3 = '3️⃣';
    public const NUM_4 = '4️⃣';
    public const NUM_5 = '5️⃣';
    public const NUM_6 = '6️⃣';
    public const NUM_7 = '7️⃣';
    public const NUM_8 = '8️⃣';
    public const NUM_9 = '9️⃣';
    public const NUM_10 = '🔟';

    // =============================================
    // Actions
    // =============================================

    public const SEARCH = '🔍';
    public const SETTINGS = '⚙️';
    public const EDIT = '✏️';
    public const DELETE = '🗑️';
    public const ADD = '➕';
    public const REMOVE = '➖';
    public const SAVE = '💾';
    public const SEND = '📤';
    public const RECEIVE = '📥';
    public const LINK = '🔗';
    public const COPY = '📋';
    public const PIN = '📌';
    public const LOCK = '🔒';
    public const UNLOCK = '🔓';
    public const KEY = '🔑';
    public const BELL = '🔔';
    public const BELL_OFF = '🔕';

    // =============================================
    // Communication
    // =============================================

    public const SPEECH = '💬';
    public const THOUGHT = '💭';
    public const MAIL = '📧';
    public const ENVELOPE = '✉️';
    public const INBOX = '📥';
    public const OUTBOX = '📤';
    public const PHONE = '📱';
    public const MEGAPHONE = '📢';
    public const LOUDSPEAKER = '📣';

    // =============================================
    // People & Faces
    // =============================================

    public const USER = '👤';
    public const USERS = '👥';
    public const ROBOT = '🤖';
    public const WAVE = '👋';
    public const THUMBS_UP = '👍';
    public const THUMBS_DOWN = '👎';
    public const CLAP = '👏';
    public const PRAY = '🙏';
    public const HEART = '❤️';
    public const FIRE = '🔥';
    public const STAR = '⭐';
    public const SPARKLES = '✨';
    public const EYES = '👀';

    // =============================================
    // Objects
    // =============================================

    public const CALENDAR = '📅';
    public const CLOCK = '🕐';
    public const ALARM = '⏰';
    public const MEMO = '📝';
    public const BOOK = '📖';
    public const FOLDER = '📁';
    public const FILE = '📄';
    public const CHART = '📊';
    public const MONEY = '💰';
    public const DOLLAR = '💵';
    public const CREDIT_CARD = '💳';
    public const GIFT = '🎁';
    public const TROPHY = '🏆';
    public const MEDAL = '🏅';
    public const CROWN = '👑';
    public const GEM = '💎';
    public const ROCKET = '🚀';
    public const BULB = '💡';
    public const GEAR = '⚙️';
    public const WRENCH = '🔧';
    public const HAMMER = '🔨';
    public const PACKAGE = '📦';
    public const TAG = '🏷️';

    // =============================================
    // Media
    // =============================================

    public const PHOTO = '📷';
    public const VIDEO = '📹';
    public const MOVIE = '🎬';
    public const MUSIC = '🎵';
    public const MICROPHONE = '🎤';
    public const HEADPHONE = '🎧';

    // =============================================
    // Weather & Nature
    // =============================================

    public const SUN = '☀️';
    public const MOON = '🌙';
    public const CLOUD = '☁️';
    public const RAIN = '🌧️';
    public const THUNDER = '⛈️';
    public const SNOW = '❄️';
    public const RAINBOW = '🌈';

    // =============================================
    // Shapes & Symbols
    // =============================================

    public const CIRCLE_RED = '🔴';
    public const CIRCLE_ORANGE = '🟠';
    public const CIRCLE_YELLOW = '🟡';
    public const CIRCLE_GREEN = '🟢';
    public const CIRCLE_BLUE = '🔵';
    public const CIRCLE_PURPLE = '🟣';
    public const CIRCLE_BROWN = '🟤';
    public const CIRCLE_BLACK = '⚫';
    public const CIRCLE_WHITE = '⚪';

    public const SQUARE_RED = '🟥';
    public const SQUARE_ORANGE = '🟧';
    public const SQUARE_YELLOW = '🟨';
    public const SQUARE_GREEN = '🟩';
    public const SQUARE_BLUE = '🟦';
    public const SQUARE_PURPLE = '🟪';
    public const SQUARE_BROWN = '🟫';
    public const SQUARE_BLACK = '⬛';
    public const SQUARE_WHITE = '⬜';

    public const DIAMOND_SMALL = '🔹';
    public const DIAMOND_LARGE = '🔷';
    public const BULLET = '•';
    public const DOT = '·';

    // =============================================
    // Country Flags (common)
    // =============================================

    public const FLAG_US = '🇺🇸';
    public const FLAG_UK = '🇬🇧';
    public const FLAG_ID = '🇮🇩';
    public const FLAG_JP = '🇯🇵';
    public const FLAG_CN = '🇨🇳';
    public const FLAG_KR = '🇰🇷';
    public const FLAG_DE = '🇩🇪';
    public const FLAG_FR = '🇫🇷';
    public const FLAG_RU = '🇷🇺';
    public const FLAG_IN = '🇮🇳';

    // =============================================
    // Helper Methods
    // =============================================

    /**
     * Get number emoji (0-10).
     */
    public static function number(int $num): string
    {
        $numbers = [
            self::NUM_0, self::NUM_1, self::NUM_2, self::NUM_3, self::NUM_4,
            self::NUM_5, self::NUM_6, self::NUM_7, self::NUM_8, self::NUM_9,
            self::NUM_10
        ];

        return $numbers[$num] ?? (string) $num;
    }

    /**
     * Get status emoji based on boolean.
     */
    public static function status(bool $success): string
    {
        return $success ? self::CHECK : self::CROSS;
    }

    /**
     * Get colored circle based on status.
     * 
     * @param string $status 'success', 'warning', 'error', 'info', 'pending'
     */
    public static function circle(string $status): string
    {
        return match (strtolower($status)) {
            'success', 'active', 'online' => self::CIRCLE_GREEN,
            'warning', 'pending' => self::CIRCLE_YELLOW,
            'error', 'failed', 'offline' => self::CIRCLE_RED,
            'info' => self::CIRCLE_BLUE,
            default => self::CIRCLE_WHITE,
        };
    }

    /**
     * Create progress bar with emoji.
     * 
     * @param int $current Current value
     * @param int $total Total value
     * @param int $length Bar length (number of segments)
     * @param string $filled Emoji for filled segment
     * @param string $empty Emoji for empty segment
     */
    public static function progressBar(
        int $current,
        int $total,
        int $length = 10,
        string $filled = '▓',
        string $empty = '░'
    ): string {
        if ($total <= 0) {
            return str_repeat($empty, $length);
        }

        $percentage = min(1, $current / $total);
        $filledCount = (int) round($percentage * $length);
        $emptyCount = $length - $filledCount;

        return str_repeat($filled, $filledCount) . str_repeat($empty, $emptyCount);
    }

    /**
     * Get all constants as array.
     */
    public static function all(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }
}
