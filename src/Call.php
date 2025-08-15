<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

/**
 * Deprecated placeholder. Call functionality was merged into `Gramsea`.
 */
final class Call
{
    public function __construct(...$args)
    {
        throw new \BadMethodCallException('Call is deprecated; use Gramsea instead.');
    }
}
