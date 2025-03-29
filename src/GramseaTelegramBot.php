<?php

namespace Andisiahaan\GramseaTelegramBot;

class GramseaTelegramBot
{
    public function sendMessage($chatId, $message)
    {
        return "Sending message to $chatId: $message";
    }
}
