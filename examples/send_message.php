<?php

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\GramseaTelegramBot\Gramsea;
use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Exception\NetworkException;

$token = getenv('GRAMSEA_BOT_TOKEN') ?: 'YOUR_BOT_TOKEN';
$chatId = getenv('GRAMSEA_CHAT_ID') ?: 'YOUR_CHAT_ID';

$bot = new Gramsea($token);

try {
    $response = $bot->sendMessage([
        'chat_id' => $chatId,
        'text' => 'Hello from Gramsea example (magic sendMessage)!',
    ]);

    echo "Response:\n";
    print_r($response);
} catch (ApiException $e) {
    echo "API error: " . $e->getMessage() . "\n";
    print_r($e->getResponse());
} catch (NetworkException $e) {
    echo "Network error: " . $e->getMessage() . "\n";
}
