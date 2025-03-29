<?php

require 'vendor/autoload.php';

use Andisiahaan\GramseaTelegramBot\GramseaTelegramBot;

$bot = new GramseaTelegramBot();
echo $bot->sendMessage(123456789, "Hello, Telegram!");
