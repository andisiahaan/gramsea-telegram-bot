<?php

require __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\GramseaTelegramBot\Gramsea;
use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Exception\NetworkException;

$token = getenv('GRAMSEA_BOT_TOKEN') ?: 'YOUR_BOT_TOKEN';
$chatId = getenv('GRAMSEA_CHAT_ID') ?: 'YOUR_CHAT_ID';

$bot = new Gramsea($token);

try {
    $photoPath = __DIR__ . '/sample.jpg'; // replace with an existing path or a remote URL

    $response = $bot->sendPhoto([
        'chat_id' => $chatId,
        'photo' => $photoPath,
        'caption' => 'Photo from Gramsea example',
    ]);

    echo "Response:\n";
    print_r($response);
} catch (ApiException $e) {
    echo "API error: " . $e->getMessage() . "\n";
    print_r($e->getResponse());
} catch (NetworkException $e) {
    echo "Network error: " . $e->getMessage() . "\n";
}
