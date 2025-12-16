<?php

use PHPUnit\Framework\TestCase;
use AndiSiahaan\GramseaTelegramBot\Sender;
use AndiSiahaan\GramseaTelegramBot\Gramsea;
use AndiSiahaan\GramseaTelegramBot\Support\InlineKeyboard;

final class SenderTest extends TestCase
{
    private const DUMMY_TOKEN = '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11';

    public function testMakeReturnsSender(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN);
        $this->assertInstanceOf(Sender::class, $sender);
    }

    public function testCreateReturnsSender(): void
    {
        $sender = Sender::create(self::DUMMY_TOKEN);
        $this->assertInstanceOf(Sender::class, $sender);
    }

    public function testChainingMethodsReturnSelf(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN);
        
        $this->assertSame($sender, $sender->to('123456'));
        $this->assertSame($sender, $sender->chat('123456'));
        $this->assertSame($sender, $sender->text('Hello'));
        $this->assertSame($sender, $sender->html('<b>Bold</b>'));
        $this->assertSame($sender, $sender->photo('https://example.com/photo.jpg'));
        $this->assertSame($sender, $sender->video('https://example.com/video.mp4'));
        $this->assertSame($sender, $sender->audio('https://example.com/audio.mp3'));
        $this->assertSame($sender, $sender->document('https://example.com/file.pdf'));
        $this->assertSame($sender, $sender->animation('https://example.com/anim.gif'));
        $this->assertSame($sender, $sender->voice('https://example.com/voice.ogg'));
        $this->assertSame($sender, $sender->media(['https://example.com/photo.jpg']));
        $this->assertSame($sender, $sender->keyboard(['inline_keyboard' => []]));
        $this->assertSame($sender, $sender->replyMarkup(['inline_keyboard' => []]));
        $this->assertSame($sender, $sender->parseMode('HTML'));
        $this->assertSame($sender, $sender->silent());
        $this->assertSame($sender, $sender->disableNotification());
        $this->assertSame($sender, $sender->protect());
        $this->assertSame($sender, $sender->protectContent());
        $this->assertSame($sender, $sender->replyTo(123));
    }

    public function testResetClearsState(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->text('Hello')
            ->photo('https://example.com/photo.jpg')
            ->silent()
            ->protect()
            ->replyTo(123);

        $sender->reset();

        // After reset, sending should throw because chat_id is empty
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Chat ID is required');
        $sender->send();
    }

    public function testSendWithoutChatIdThrowsException(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN)->text('Hello');
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Chat ID is required');
        $sender->send();
    }

    public function testSendWithoutTextOrMediaThrowsException(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN)->to('123456');
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Either text or media is required');
        $sender->send();
    }

    public function testMarkdownToHtmlConversion(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->text('**bold** and *italic* and `code`');
        
        $reflection = new ReflectionClass($sender);
        $property = $reflection->getProperty('text');
        $property->setAccessible(true);
        
        $text = $property->getValue($sender);
        
        $this->assertStringContainsString('<b>bold</b>', $text);
        $this->assertStringContainsString('<i>italic</i>', $text);
        $this->assertStringContainsString('<code>code</code>', $text);
    }

    public function testHtmlMethodDoesNotConvertMarkdown(): void
    {
        $rawHtml = '<b>already bold</b>';
        
        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->html($rawHtml);
        
        $reflection = new ReflectionClass($sender);
        $property = $reflection->getProperty('text');
        $property->setAccessible(true);
        
        $this->assertSame($rawHtml, $property->getValue($sender));
    }

    public function testKeyboardWithInlineKeyboard(): void
    {
        $keyboard = InlineKeyboard::make()
            ->callback('Button 1', 'action1')
            ->callback('Button 2', 'action2');

        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->text('Hello')
            ->keyboard($keyboard);

        $reflection = new ReflectionClass($sender);
        $property = $reflection->getProperty('replyMarkup');
        $property->setAccessible(true);
        
        $markup = $property->getValue($sender);
        
        $this->assertIsArray($markup);
        $this->assertArrayHasKey('inline_keyboard', $markup);
    }

    public function testKeyboardWithJsonString(): void
    {
        $json = '{"inline_keyboard":[[{"text":"Test","callback_data":"test"}]]}';
        
        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->text('Hello')
            ->keyboard($json);

        $reflection = new ReflectionClass($sender);
        $property = $reflection->getProperty('replyMarkup');
        $property->setAccessible(true);
        
        $markup = $property->getValue($sender);
        
        $this->assertIsArray($markup);
        $this->assertArrayHasKey('inline_keyboard', $markup);
    }

    public function testKeyboardWithNull(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->text('Hello')
            ->keyboard(['inline_keyboard' => []])
            ->keyboard(null); // Clear keyboard

        $reflection = new ReflectionClass($sender);
        $property = $reflection->getProperty('replyMarkup');
        $property->setAccessible(true);
        
        $this->assertNull($property->getValue($sender));
    }

    public function testGetBotReturnsGramseaInstance(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN);
        $bot = $sender->getBot();
        
        $this->assertInstanceOf(Gramsea::class, $bot);
    }

    public function testMediaStoresUrls(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->media('https://example.com/photo.jpg');

        $reflection = new ReflectionClass($sender);
        $property = $reflection->getProperty('media');
        $property->setAccessible(true);
        
        $media = $property->getValue($sender);
        
        $this->assertCount(1, $media);
        $this->assertSame('https://example.com/photo.jpg', $media[0]);
    }

    public function testMultipleMediaAsArray(): void
    {
        $sender = Sender::make(self::DUMMY_TOKEN)
            ->to('123456')
            ->media([
                'https://example.com/photo1.jpg',
                'https://example.com/photo2.png',
            ]);

        $reflection = new ReflectionClass($sender);
        $property = $reflection->getProperty('media');
        $property->setAccessible(true);
        
        $media = $property->getValue($sender);
        
        $this->assertCount(2, $media);
    }
}
