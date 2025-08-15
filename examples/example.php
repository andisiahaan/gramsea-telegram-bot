<?php

require __DIR__ . '/../vendor/autoload.php';

use Andisiahaan\GramseaTelegramBot\Gramsea;

$token = getenv('GRAMSEA_BOT_TOKEN') ?: 'YOUR_BOT_TOKEN';
$bot = new Gramsea($token);

// Contoh: cek token
var_dump($bot->getMe());
