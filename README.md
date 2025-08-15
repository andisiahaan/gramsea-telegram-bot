# Gramsea Telegram Bot PHP Library

Sebuah library sederhana untuk memanggil Telegram Bot API.

## Instalasi

```bash
composer require andisiahaan/gramsea-telegram-bot
```

## Penggunaan singkat

```php
use Andisiahaan\GramseaTelegramBot\Gramsea;

$bot = new Gramsea('YOUR_BOT_TOKEN');
$bot->sendMessage(123456789, 'Halo dari Gramsea!');
```

Note: Gramsea menyediakan magic method `__call` sehingga Anda dapat memanggil metode API langsung
seperti `sendMessage`, `sendPhoto`, dll. Kedua contoh di folder `examples/` menunjukkan cara
memanggilnya dengan format parameter array.

Contoh menjalankan:

```powershell
cd examples
php send_message.php
php send_photo.php
```

## Struktur

- `src/` - Sumber library
- `examples/` - Contoh penggunaan
- `tests/` - Unit tests

## Lisensi
MIT
