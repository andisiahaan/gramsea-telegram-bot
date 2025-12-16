<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Helper class untuk encode/decode callback data.
 * 
 * Telegram callback_data dibatasi 64 bytes, class ini membantu
 * mengoptimalkan penggunaan space tersebut.
 */
class CallbackData
{
    protected const SEPARATOR = ':';
    protected const MAX_LENGTH = 64;

    /**
     * Encode callback data dari array.
     * 
     * @param string $action Action name
     * @param array $params Additional parameters
     * @return string Encoded callback data
     * 
     * Example:
     * CallbackData::encode('user', ['id' => 123, 'action' => 'view'])
     * // Returns: 'user:123:view'
     */
    public static function encode(string $action, array $params = []): string
    {
        $parts = [$action];
        
        foreach ($params as $value) {
            $parts[] = (string) $value;
        }
        
        $result = implode(self::SEPARATOR, $parts);
        
        // Warn if exceeds limit
        if (strlen($result) > self::MAX_LENGTH) {
            trigger_error(
                "Callback data exceeds 64 bytes limit: " . strlen($result) . " bytes",
                E_USER_WARNING
            );
        }
        
        return $result;
    }

    /**
     * Decode callback data to array.
     * 
     * @param string $data Callback data string
     * @return array [action, ...params]
     * 
     * Example:
     * CallbackData::decode('user:123:view')
     * // Returns: ['user', '123', 'view']
     */
    public static function decode(string $data): array
    {
        return explode(self::SEPARATOR, $data);
    }

    /**
     * Parse callback data dengan nama parameter.
     * 
     * @param string $data Callback data string
     * @param array $paramNames Parameter names untuk mapping
     * @return array Associative array dengan 'action' dan named params
     * 
     * Example:
     * CallbackData::parse('user:123:view', ['id', 'action'])
     * // Returns: ['action' => 'user', 'id' => '123', 'action' => 'view']
     */
    public static function parse(string $data, array $paramNames = []): array
    {
        $parts = self::decode($data);
        $result = ['action' => array_shift($parts) ?? ''];
        
        foreach ($paramNames as $index => $name) {
            $result[$name] = $parts[$index] ?? null;
        }
        
        // Add remaining unnamed params
        $remainingIndex = count($paramNames);
        for ($i = $remainingIndex; $i < count($parts); $i++) {
            $result['param_' . ($i - $remainingIndex)] = $parts[$i];
        }
        
        return $result;
    }

    /**
     * Get action from callback data.
     */
    public static function getAction(string $data): string
    {
        $parts = self::decode($data);
        return $parts[0] ?? '';
    }

    /**
     * Get parameter at specific index.
     * 
     * @param string $data Callback data
     * @param int $index Parameter index (0 = first param after action)
     */
    public static function getParam(string $data, int $index): ?string
    {
        $parts = self::decode($data);
        return $parts[$index + 1] ?? null;
    }

    /**
     * Check if callback matches an action.
     */
    public static function is(string $data, string $action): bool
    {
        return self::getAction($data) === $action;
    }

    /**
     * Check if callback starts with a prefix.
     */
    public static function startsWith(string $data, string $prefix): bool
    {
        return str_starts_with($data, $prefix . self::SEPARATOR) || $data === $prefix;
    }

    /**
     * Create callback with compact integer encoding.
     * Convert large integers to base62 for shorter strings.
     * 
     * @param string $action Action name
     * @param array $params Parameters (integers will be base62 encoded)
     */
    public static function compact(string $action, array $params = []): string
    {
        $encodedParams = [];
        
        foreach ($params as $value) {
            if (is_int($value) && $value >= 0) {
                $encodedParams[] = self::encodeBase62($value);
            } else {
                $encodedParams[] = (string) $value;
            }
        }
        
        return self::encode($action, $encodedParams);
    }

    /**
     * Parse compact callback data.
     * 
     * @param string $data Callback data
     * @param array $intParams Indices of params that should be decoded as integers
     */
    public static function parseCompact(string $data, array $intParams = []): array
    {
        $parts = self::decode($data);
        $action = array_shift($parts) ?? '';
        
        $result = ['action' => $action];
        
        foreach ($parts as $index => $value) {
            if (in_array($index, $intParams)) {
                $result['param_' . $index] = self::decodeBase62($value);
            } else {
                $result['param_' . $index] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Encode integer to base62.
     */
    public static function encodeBase62(int $num): string
    {
        if ($num === 0) {
            return '0';
        }

        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $base = strlen($chars);
        $result = '';

        while ($num > 0) {
            $result = $chars[$num % $base] . $result;
            $num = intdiv($num, $base);
        }

        return $result;
    }

    /**
     * Decode base62 to integer.
     */
    public static function decodeBase62(string $str): int
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $base = strlen($chars);
        $result = 0;

        for ($i = 0; $i < strlen($str); $i++) {
            $result = $result * $base + strpos($chars, $str[$i]);
        }

        return $result;
    }

    /**
     * Get remaining bytes available.
     */
    public static function remainingBytes(string $data): int
    {
        return max(0, self::MAX_LENGTH - strlen($data));
    }

    /**
     * Check if callback data is within limit.
     */
    public static function isValid(string $data): bool
    {
        return strlen($data) <= self::MAX_LENGTH;
    }
}
