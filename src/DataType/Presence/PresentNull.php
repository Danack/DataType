<?php

declare(strict_types=1);

namespace DataType\Presence;

/**
 * The field was present with an explicit JSON null (PATCH: clear the value).
 */
final class PresentNull
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }
}
