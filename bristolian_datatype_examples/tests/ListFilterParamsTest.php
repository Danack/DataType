<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Tests;

use BristolianDatatypeExamples\Runnable\ListFilterParams;
use PHPUnit\Framework\TestCase;

class ListFilterParamsTest extends TestCase
{
    public function testCreateFromArray_uses_defaults_when_keys_missing(): void
    {
        $params = ListFilterParams::createFromArray([]);

        $this->assertNull($params->query);
        $this->assertSame(20, $params->limit);
        $this->assertFalse($params->include_archived);
    }
}
