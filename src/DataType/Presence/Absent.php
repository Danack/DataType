<?php

declare(strict_types=1);

namespace DataType\Presence;

/**
 * The input field was not present (PATCH: do not apply an update).
 */
final class Absent
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
