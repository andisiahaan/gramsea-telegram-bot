<?php

namespace Andisiahaan\GramseaTelegramBot;

class Call
{
    protected string $baseUrl;

    public function __construct(string $botToken)
    {
        $this->baseUrl = "https://api.telegram.org/bot{$botToken}/";
    }
    
    /**
     * Magic method to directly call Telegram API methods
     *
     * @param string $method Telegram Bot API method name
     * @param array $arguments Arguments passed to the method (first argument must be an array of parameters)
     * @return array Response from Telegram API
     */
    public function __call(string $method, array $arguments): array
    {
        $parameters = $arguments[0] ?? [];
        return $this->callMethod($method, $parameters);
    }

    /**
     * General method to access all Telegram Bot API methods
     *
     * @param string $method Telegram Bot API method name
     * @param array $parameters Parameters for the method
     * @return array Response from Telegram API
     */
    public function callMethod(string $method, array $parameters = []): array
    {
        return Curl::request($this->baseUrl . $method, $parameters);
    }


}
