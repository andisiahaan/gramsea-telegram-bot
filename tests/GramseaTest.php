<?php

use PHPUnit\Framework\TestCase;
use AndiSiahaan\GramseaTelegramBot\Gramsea;

final class GramseaTest extends TestCase
{
    public function testClassExistsAndHasGetMe()
    {
        $this->assertTrue(class_exists(Gramsea::class));
        $reflection = new ReflectionClass(Gramsea::class);
        $this->assertTrue($reflection->hasMethod('getMe'));
    }
}
