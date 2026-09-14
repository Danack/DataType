<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Tests;

use BristolianDatatypeExamples\Runnable\UpdateProfileParams;
use PHPUnit\Framework\TestCase;
use function BristolianDatatypeExamples\Runnable\queryRequest;

class UpdateProfileParamsTest extends TestCase
{
    public function testCreateFromRequest_reads_display_name(): void
    {
        $params = UpdateProfileParams::createFromRequest(queryRequest([
            'display_name' => 'Dan',
        ]));

        $this->assertSame('Dan', $params->display_name);
        $this->assertNull($params->bio);
    }
}
