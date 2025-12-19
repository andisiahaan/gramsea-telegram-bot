# Gramsea Telegram Bot PHP Library

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

Library PHP sederhana dan powerful untuk berinteraksi dengan Telegram Bot API tanpa dependensi framework.

## ✨ Fitur

- 🚀 **Zero Framework Dependencies** - PHP murni, bisa digunakan di mana saja
- 🎯 **Magic Method** - Panggil method API Telegram langsung
- 📦 **Fluent Sender Classes** - Method chaining untuk mengirim pesan
- 📨 **Update Wrapper** - Object-oriented access ke webhook data
- 🖼️ **Media Helper** - Deteksi otomatis tipe file
- ⌨️ **Keyboard Builder** - Fluent builder untuk inline & reply keyboard
- 🔘 **Callback Data** - Encode/decode callback data dengan kompresi
- 🔗 **Deep Link** - Generate & parse deep links dan referral codes
- ✍️ **Text Formatter** - Fluent builder untuk formatting HTML
- 📏 **Limits Helper** - Constants dan validasi untuk Telegram limits
- 😀 **Emoji Constants** - Collection emoji yang sering dipakai
- 🔧 **Moderation Tools** - Ban, mute, restrict, pin messages
- ⚠️ **Exception Handling** - Custom exception untuk error handling

## 📦 Instalasi

```bash
composer require andisiahaan/gramsea-telegram-bot
```

## 🚀 Quick Start

### Penggunaan Dasar

```php
use AndiSiahaan\GramseaTelegramBot\Gramsea;

$bot = new Gramsea('YOUR_BOT_TOKEN');

// Kirim pesan
$bot->sendMessage([
    'chat_id' => 123456789,
    'text' => 'Halo dari Gramsea!'
]);

// Kirim photo
$bot->sendPhoto([
    'chat_id' => 123456789,
    'photo' => 'https://example.com/image.jpg',
    'caption' => 'Photo caption'
]);
```

### Menggunakan Fluent Sender (Recommended)

```php
use AndiSiahaan\GramseaTelegramBot\Gramsea;
use AndiSiahaan\GramseaTelegramBot\Support\InlineKeyboard;

$bot = new Gramsea('YOUR_BOT_TOKEN');

// Text message dengan keyboard
$bot->message()
    ->to($chatId)
    ->text('Hello **world**!')  // Markdown otomatis dikonversi ke HTML
    ->keyboard(InlineKeyboard::make()->callback('Click Me', 'action'))
    ->send();

// Photo dengan caption
$bot->media()
    ->to($chatId)
    ->photo('https://example.com/image.jpg')
    ->caption('Check this out!')
    ->silent()
    ->send();

// Media group (album)
$bot->mediaGroup()
    ->to($chatId)
    ->photo('photo1.jpg')
    ->photo('photo2.jpg')
    ->video('video.mp4')
    ->caption('My album!')
    ->send();

// Text dengan link preview options
$bot->text()
    ->to($chatId)
    ->text('Check this article!')
    ->previewUrl('https://example.com/article')
    ->largePreview()
    ->send();
```

---

## 📖 Dokumentasi

### Class `Gramsea`

Class utama dengan magic method untuk semua Telegram Bot API.

```php
$bot = new Gramsea('YOUR_BOT_TOKEN');

// Magic method - panggil API langsung
$bot->sendMessage([...]);
$bot->sendPhoto([...]);
$bot->sendDocument([...]);
// dll...
```

#### Sender Factory Methods

```php
// MessageSender - auto-delegates based on content
$bot->message()->to($chatId)->text('Hello!')->send();

// TextSender - text dengan link preview options
$bot->text()->to($chatId)->text('Hello!')->noPreview()->send();

// MediaSender - single media
$bot->media()->to($chatId)->photo('url')->caption('Nice!')->send();

// MediaGroupSender - album (2-10 items)
$bot->mediaGroup()->to($chatId)->photo('a.jpg')->photo('b.jpg')->send();
```

#### Webhook Management

```php
$bot->setWebhook('https://yourdomain.com/webhook');
$bot->deleteWebhook();
$bot->getWebhookInfo();
```

#### Chat Member Checks

```php
$bot->isChatMember($chatId, $userId);   // bool
$bot->isChatAdmin($chatId, $userId);    // bool
$bot->isChatCreator($chatId, $userId);  // bool
$bot->getChatMemberStatus($chatId, $userId); // string|null
```

#### Chat Actions

```php
$bot->sendTyping($chatId);
$bot->sendUploadPhotoAction($chatId);
$bot->sendUploadDocumentAction($chatId);
$bot->sendUploadVideoAction($chatId);
$bot->sendRecordVideoAction($chatId);
$bot->sendRecordVoiceAction($chatId);
$bot->sendChatAction($chatId, 'typing');
```

#### Moderation

```php
// Ban & Unban
$bot->banChatMember($chatId, $userId, $untilDate, $revokeMessages);
$bot->unbanChatMember($chatId, $userId);

// Mute & Unmute
$bot->muteUser($chatId, $userId, $untilDate);
$bot->unmuteUser($chatId, $userId);

// Restrict & Promote
$bot->restrictChatMember($chatId, $userId, $permissions, $untilDate);
$bot->promoteChatMember($chatId, $userId, $rights);

// Messages
$bot->deleteMessage($chatId, $messageId);
$bot->deleteMessages($chatId, $messageIds);
$bot->pinChatMessage($chatId, $messageId);
$bot->unpinChatMessage($chatId, $messageId);
$bot->unpinAllChatMessages($chatId);
```

#### Keyboard Builders

```php
$inline = $bot->inlineKeyboard([
    [
        ['text' => 'Button 1', 'callback_data' => 'btn1'],
        ['text' => 'Button 2', 'url' => 'https://example.com']
    ]
]);

$reply = $bot->replyKeyboard([
    [['text' => 'Option A'], ['text' => 'Option B']]
]);

$bot->removeKeyboard();
$bot->forceReply();
```

#### Bot Commands

```php
$bot->setCommands([
    'start' => 'Mulai bot',
    'help' => 'Bantuan'
]);
$bot->getCommands();
$bot->deleteCommands();
```

#### File Operations

```php
$url = $bot->getFileUrl($fileId);
$content = $bot->downloadFile($fileId);
```

---

## 📨 Sender Classes

Library menyediakan 4 sender class dengan fluent chaining API:

| Class | Kegunaan |
|-------|----------|
| `MessageSender` | General sender, auto-delegates ke sender yang tepat |
| `TextSender` | Khusus text message dengan link preview options |
| `MediaSender` | Khusus single media (photo, video, audio, document) |
| `MediaGroupSender` | Khusus media group/album (2-10 items) |

### MessageSender (Recommended)

Auto-detects dan delegates ke sender yang tepat:
- Text only → `TextSender`
- 1 media → `MediaSender`
- 2+ media → `MediaGroupSender`

```php
use AndiSiahaan\GramseaTelegramBot\MessageSender;

// Text only
MessageSender::bot($bot)
    ->to($chatId)
    ->text('Hello **world**!')
    ->keyboard($keyboard)
    ->send();

// Single media
MessageSender::bot($bot)
    ->to($chatId)
    ->photo('https://example.com/image.jpg')
    ->text('Caption here')
    ->send();

// Media group
MessageSender::bot($bot)
    ->to($chatId)
    ->photo('photo1.jpg')
    ->photo('photo2.jpg')
    ->text('Album!')
    ->send();
```

### TextSender

Untuk text message dengan kontrol penuh atas link preview.

```php
use AndiSiahaan\GramseaTelegramBot\TextSender;

TextSender::bot($bot)
    ->to($chatId)
    ->text('Check this: https://example.com')
    ->noPreview()           // Disable link preview
    ->send();

// Advanced link preview options
TextSender::bot($bot)
    ->to($chatId)
    ->text('Amazing article!')
    ->previewUrl('https://example.com/article')
    ->largePreview()        // Prefer large media
    ->previewAboveText()    // Show preview above text
    ->send();
```

**Link Preview Methods:**
- `noPreview()` - Disable link preview
- `previewUrl($url)` - Set custom URL for preview
- `smallPreview()` - Prefer small media
- `largePreview()` - Prefer large media
- `previewAboveText()` - Show preview above message text

### MediaSender

Untuk single media dengan caption.

```php
use AndiSiahaan\GramseaTelegramBot\MediaSender;

MediaSender::bot($bot)
    ->to($chatId)
    ->photo('https://example.com/image.jpg')
    ->caption('Check this out!')
    ->keyboard($keyboard)
    ->send();

// Dengan auto-detect media type
MediaSender::bot($bot)
    ->to($chatId)
    ->media('https://example.com/video.mp4')  // Auto-detect: video
    ->caption('My video')
    ->send();
```

**Media Methods:**
- `photo($url)`, `video($url)`, `audio($url)`, `document($url)`, `animation($url)`, `voice($url)`
- `media($url)` - Auto-detect type dari extension
- `caption($text)` - Set caption (supports markdown)
- `captionHtml($html)` - Set caption tanpa konversi

### MediaGroupSender

Untuk mengirim album (2-10 media items).

```php
use AndiSiahaan\GramseaTelegramBot\MediaGroupSender;

MediaGroupSender::bot($bot)
    ->to($chatId)
    ->photo('photo1.jpg')
    ->photo('photo2.jpg')
    ->video('video.mp4')
    ->caption('My album!')
    ->send();

// Dari array
MediaGroupSender::bot($bot)
    ->to($chatId)
    ->media(['photo1.jpg', 'photo2.jpg', 'photo3.jpg'])
    ->caption('Photos collection')
    ->send();
```

**Media Group Rules:**
- ✅ Photo + Video = OK
- ✅ Document + Document = OK
- ✅ Audio + Audio = OK
- ❌ Photo + Document = NOT OK
- ❌ Video + Audio = NOT OK

### Common Chaining Methods

Semua sender class memiliki method berikut:

```php
->to($chatId)           // Set target chat
->keyboard($markup)     // Set reply markup
->parseMode('HTML')     // Set parse mode
->silent()              // Disable notification
->protect()             // Protect content from forwarding
->replyTo($messageId)   // Reply to message
->allowPaidBroadcast()  // Allow paid broadcast
->send()                // Execute
->reset()               // Reset state for reuse
```

---

## 📨 Update Wrapper

Object-oriented wrapper untuk mengakses data webhook.

```php
use AndiSiahaan\GramseaTelegramBot\Support\Update;

// Dari webhook
$update = Update::fromWebhook();

// Atau dari JSON/array
$update = Update::fromJson($jsonString);
$update = Update::fromArray($data);

// Check update type
if ($update->isMessage()) {
    $msg = $update->message();
    
    echo $msg->text();
    echo $msg->from()->fullName();
    echo $msg->chat()->id();
    echo $msg->chat()->type();
    
    // Command parsing
    if ($msg->isCommand()) {
        echo $msg->command();      // 'start'
        echo $msg->commandArgs();  // 'ref123'
    }
    
    // Media checks
    if ($msg->hasPhoto()) {
        $photo = $msg->largestPhoto();
    }
}

// Shortcuts
echo $update->text();       // $update->anyMessage()->text()
echo $update->chatId();     // $update->anyMessage()->chatId()
echo $update->fromName();   // $update->from()->fullName()

// Callback query
if ($update->isCallbackQuery()) {
    echo $update->callbackData();
    echo $update->callbackQueryId();
}
```

**Type Checks:**
- `isMessage()`, `isEditedMessage()`, `isChannelPost()`, `isEditedChannelPost()`
- `isCallbackQuery()`, `isInlineQuery()`
- `isMyChatMember()`, `isChatMember()`, `isChatJoinRequest()`
- `hasMessage()` - any message type

**Message Object:**
- `text()`, `caption()`, `textOrCaption()`
- `from()`, `chat()`, `chatId()`, `fromId()`
- `hasPhoto()`, `hasVideo()`, `hasDocument()`, `hasMedia()`
- `isCommand()`, `command()`, `commandArgs()`
- `isReply()`, `replyToMessage()`

**Chat Object:**
- `id()`, `type()`, `name()`, `username()`
- `isPrivate()`, `isGroup()`, `isSupergroup()`, `isChannel()`

**User Object:**
- `id()`, `fullName()`, `username()`, `languageCode()`
- `isPremium()`, `mentionHtml()`

---

### Support Classes

#### `Support\DeepLink`

Generate dan parse Telegram deep links.

```php
use AndiSiahaan\GramseaTelegramBot\Support\DeepLink;

// Generate links
DeepLink::start('mybot', 'ref123');  // https://t.me/mybot?start=ref123
DeepLink::startGroup('mybot');        // https://t.me/mybot?startgroup
DeepLink::startChannel('mybot');      // https://t.me/mybot?startchannel
DeepLink::chat('username');           // https://t.me/username
DeepLink::message('channel', 123);    // https://t.me/channel/123
DeepLink::share('https://url.com', 'Check this!');

// Parse start parameter
$param = DeepLink::parseStartParameter('/start ref123');  // 'ref123'

// Referral codes
$code = DeepLink::generateReferralCode(123456, 'ref');  // 'ref_MTIzNDU2'
$userId = DeepLink::decodeReferralCode('ref_MTIzNDU2', 'ref');  // '123456'
```

#### `Support\TextFormatter`

Fluent builder untuk HTML formatting.

```php
use AndiSiahaan\GramseaTelegramBot\Support\TextFormatter;

// Fluent builder
$text = TextFormatter::make()
    ->bold('Welcome!')
    ->newLine()
    ->text('Hello, ')
    ->mention('John', 123456789)
    ->newLine(2)
    ->italic('Status: ')
    ->code('Active')
    ->toString();

// Static shortcuts
echo TextFormatter::b('bold');           // <b>bold</b>
echo TextFormatter::i('italic');         // <i>italic</i>
echo TextFormatter::c('code');           // <code>code</code>
echo TextFormatter::a('Click', 'url');   // <a href="url">Click</a>
echo TextFormatter::user('John', 123);   // <a href="tg://user?id=123">John</a>

// Markdown to HTML conversion
$html = TextFormatter::markdownToHtml('**bold** and *italic*');
// Output: <b>bold</b> and <i>italic</i>
```

#### `Support\ReplyMarkup`

Format keyboard markup dengan auto row wrapping.

```php
use AndiSiahaan\GramseaTelegramBot\Support\ReplyMarkup;

// Inline dengan URL
ReplyMarkup::inlineUrl([
    'Google' => 'https://google.com',
    'GitHub' => 'https://github.com'
]);

// Inline dengan callback
ReplyMarkup::inlineCallback([
    'Yes' => 'answer_yes',
    'No' => 'answer_no'
]);

// Reply keyboard
ReplyMarkup::reply(['Option A', 'Option B', 'Option C']);

// Remove & Force reply
ReplyMarkup::remove();
ReplyMarkup::forceReply(false, 'Type here...');
```

#### `Support\Limit`

Constants untuk Telegram API limits.

```php
use AndiSiahaan\GramseaTelegramBot\Support\Limit;

// Constants
Limit::MESSAGE_TEXT;        // 4096
Limit::CAPTION;             // 1024
Limit::CALLBACK_DATA;       // 64
Limit::MEDIA_GROUP_MAX;     // 10
Limit::START_PARAMETER;     // 64

// Helper methods
Limit::truncateMessage($longText);     // Truncate dengan '...'
Limit::truncateCaption($longText);
Limit::splitText($veryLongText);       // Split ke array chunks
Limit::exceedsMessageLimit($text);     // bool
Limit::isValidStartParameter($param);  // bool
```

#### `Support\Emoji`

Emoji constants yang sering dipakai.

```php
use AndiSiahaan\GramseaTelegramBot\Support\Emoji;

echo Emoji::CHECK;      // ✅
echo Emoji::CROSS;      // ❌
echo Emoji::WARNING;    // ⚠️
echo Emoji::FIRE;       // 🔥
echo Emoji::ROCKET;     // 🚀

// Helpers
Emoji::status(true);    // ✅ atau ❌
Emoji::number(5);       // 5️⃣
Emoji::circle('success'); // 🟢

// Progress bar
Emoji::progressBar(7, 10);  // ▓▓▓▓▓▓▓░░░
```

#### `Support\MediaHelper`

Deteksi tipe media dari file extension.

```php
use AndiSiahaan\GramseaTelegramBot\Support\MediaHelper;

MediaHelper::getMediaType('photo.jpg');   // 'photo'
MediaHelper::getMediaType('video.mp4');   // 'video'
MediaHelper::getMimeType('doc.pdf');      // 'application/pdf'

MediaHelper::isImage('photo.png');  // true
MediaHelper::isVideo('clip.mp4');   // true
MediaHelper::isAudio('song.mp3');   // true
```

#### `Support\InlineKeyboard`

Fluent builder untuk inline keyboard dengan pattern umum.

```php
use AndiSiahaan\GramseaTelegramBot\Support\InlineKeyboard;

// Fluent builder
$keyboard = InlineKeyboard::make()
    ->callback('Option 1', 'opt_1')
    ->callback('Option 2', 'opt_2')
    ->row()
    ->url('Visit Site', 'https://example.com')
    ->row()
    ->back('main_menu')
    ->toArray();

// Pagination
$keyboard = InlineKeyboard::make()
    ->pagination(currentPage: 3, totalPages: 10, callbackPrefix: 'page_')
    ->toArray();

// Confirmation
$keyboard = InlineKeyboard::make()
    ->confirm('delete_yes_123', 'delete_no')
    ->toArray();

// Grid layout
$keyboard = InlineKeyboard::make()
    ->grid([
        ['text' => 'A', 'callback' => 'a'],
        ['text' => 'B', 'callback' => 'b'],
        ['text' => 'C', 'callback' => 'c'],
        ['text' => 'D', 'callback' => 'd'],
    ], columns: 2)
    ->toArray();

// Static helpers
InlineKeyboard::yesNo('confirm_yes', 'confirm_no');
InlineKeyboard::singleUrl('Click Here', 'https://example.com');
```

#### `Support\CallbackData`

Encode/decode callback data dengan kompresi untuk ID besar.

```php
use AndiSiahaan\GramseaTelegramBot\Support\CallbackData;

// Encode callback
$data = CallbackData::encode('user', ['123', 'view']);  // 'user:123:view'

// Decode callback
$parts = CallbackData::decode('user:123:view');  // ['user', '123', 'view']

// Parse dengan named params
$parsed = CallbackData::parse('user:123:view', ['id', 'action']);
// ['action' => 'user', 'id' => '123', 'action' => 'view']

// Check action
CallbackData::is($data, 'user');           // true
CallbackData::startsWith($data, 'user');   // true
CallbackData::getAction($data);            // 'user'
CallbackData::getParam($data, 0);          // '123'

// Compact encoding untuk ID besar (base62)
$compact = CallbackData::compact('u', [1234567890]);  // 'u:1LY7VK' (lebih pendek!)
$parsed = CallbackData::parseCompact('u:1LY7VK', [0]); // decode balik ke integer

// Validasi
CallbackData::isValid($data);        // true jika <= 64 bytes
CallbackData::remainingBytes($data); // sisa bytes yang tersedia
```

---

### Curl Configuration

```php
use AndiSiahaan\GramseaTelegramBot\Curl;

// Set timeout
Curl::setTimeout(60);        // Request timeout
Curl::setConnectTimeout(15); // Connection timeout

// Enable retry
Curl::setRetry(3, 1000);     // 3 attempts, 1 second delay

// Download file
Curl::download('https://example.com/file.pdf', '/path/to/save.pdf');
```

---

### Exception Handling

Library menyediakan exception classes yang spesifik untuk berbagai error dari Telegram API:

| Exception | Error Code | Keterangan |
|-----------|------------|------------|
| `BadRequestException` | 400 | Parameter tidak valid |
| `UnauthorizedException` | 401 | Bot token tidak valid |
| `ForbiddenException` | 403 | Bot tidak punya akses (diblok, di-kick, dll) |
| `NotFoundException` | 404 | Chat/user/message tidak ditemukan |
| `ConflictException` | 409 | Conflict webhook/polling |
| `TooManyRequestsException` | 429 | Rate limit tercapai |
| `TelegramServerException` | 500+ | Server Telegram error |
| `ApiException` | - | Base class untuk semua API errors |
| `NetworkException` | - | Network/connection errors |

#### Basic Usage

```php
use AndiSiahaan\GramseaTelegramBot\Gramsea;
use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Exception\ForbiddenException;
use AndiSiahaan\GramseaTelegramBot\Exception\TooManyRequestsException;
use AndiSiahaan\GramseaTelegramBot\Exception\NetworkException;

try {
    $bot->sendMessage([...]);
} catch (ForbiddenException $e) {
    // Bot diblok atau tidak punya akses
    if ($e->isBotBlocked()) {
        echo "User telah memblokir bot";
    } elseif ($e->isBotKicked()) {
        echo "Bot telah di-kick dari group";
    }
} catch (TooManyRequestsException $e) {
    // Rate limited - tunggu dan retry
    sleep($e->getWaitTime());
    // retry...
} catch (ApiException $e) {
    // Catch-all untuk API error lain
    echo "Error: " . $e->getMessage();
    echo "Code: " . $e->getErrorCode();
} catch (NetworkException $e) {
    // Network error (timeout, connection, SSL)
    if ($e->isTimeout()) {
        echo "Request timed out";
    }
}
```

#### Exception Methods

**ApiException (Base Class)**
```php
$e->getMessage();        // Pesan error dari Telegram
$e->getErrorCode();      // HTTP error code (400, 403, 429, dll)
$e->getDescription();    // Description dari Telegram response
$e->getResponse();       // Full response array
$e->getRetryAfter();     // Waktu tunggu (untuk rate limit), null jika tidak ada
$e->getMigrateToChatId(); // Chat ID baru (untuk supergroup migration)
$e->isRetryable();       // True jika error bisa di-retry
```

**ForbiddenException**
```php
$e->isBotBlocked();        // Bot diblok user
$e->isBotKicked();         // Bot di-kick dari group
$e->hasNoRightsToSend();   // Bot tidak punya hak kirim pesan
$e->isUserDeactivated();   // User sudah dihapus/nonaktif
```

**NotFoundException**
```php
$e->isChatNotFound();      // Chat tidak ditemukan
$e->isMessageNotFound();   // Message tidak ditemukan
```

**ConflictException**
```php
$e->isWebhookConflict();     // Conflict karena webhook
$e->isGetUpdatesConflict();  // Conflict karena getUpdates aktif
```

**TooManyRequestsException**
```php
$e->getWaitTime();   // Waktu tunggu dalam detik (default 30)
$e->isRetryable();   // Selalu true
```

**NetworkException**
```php
$e->getCurlErrorCode();  // cURL error code
$e->isTimeout();         // Timeout error
$e->isConnectionError(); // Connection error
$e->isSslError();        // SSL certificate error
```

---

## 📁 Struktur Library

```
src/
├── Gramsea.php           # Class utama dengan magic method
├── BaseSender.php        # Abstract base class untuk senders
├── MessageSender.php     # General sender (auto-delegates)
├── TextSender.php        # Text message sender
├── MediaSender.php       # Single media sender
├── MediaGroupSender.php  # Media group sender
├── Curl.php              # HTTP client
├── HelperMethods.php     # Trait helper methods
├── Exception/
│   ├── ApiException.php           # Base exception untuk API errors
│   ├── BadRequestException.php    # 400 errors
│   ├── UnauthorizedException.php  # 401 errors
│   ├── ForbiddenException.php     # 403 errors
│   ├── NotFoundException.php      # 404 errors
│   ├── ConflictException.php      # 409 errors
│   ├── TooManyRequestsException.php # 429 rate limit
│   ├── TelegramServerException.php  # 500+ server errors
│   └── NetworkException.php       # Network/connection errors
└── Support/
    ├── CallbackData.php   # Callback data encoder
    ├── Chat.php           # Chat object wrapper
    ├── DeepLink.php       # Deep link generator
    ├── Emoji.php          # Emoji constants
    ├── InlineKeyboard.php # Inline keyboard builder
    ├── Limit.php          # API limits
    ├── MediaHelper.php    # Media type detection
    ├── Message.php        # Message object wrapper
    ├── ReplyMarkup.php    # Keyboard formatting
    ├── TextFormatter.php  # HTML text formatting
    ├── Update.php         # Update wrapper
    └── User.php           # User object wrapper
```

## 🧪 Testing

```bash
composer test
```

## 📄 License

MIT License - lihat [LICENSE](LICENSE)

## 👤 Author

**Andi Saputra Siahaan**
- Website: [andipedia.com](https://andipedia.com)
- Email: andisiahaan670@gmail.com
