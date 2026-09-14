<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Tests;

use BristolianDatatypeExamples\Runnable\RenameParams;
use PHPUnit\Framework\TestCase;

class RenameParamsTest extends TestCase
{
    public function testCreateOrErrorFromArray_returns_problems_when_name_missing(): void
    {
        [$params, $validationProblems] = RenameParams::createOrErrorFromArray([]);

        $this->assertNull($params);
        $this->assertNotSame([], $validationProblems);
    }

    public function testCreateOrErrorFromArray_returns_params_when_valid(): void
    {
        [$params, $validationProblems] = RenameParams::createOrErrorFromArray([
            'name' => 'new-name',
        ]);

        $this->assertSame([], $validationProblems);
        $this->assertInstanceOf(RenameParams::class, $params);
        $this->assertSame('new-name', $params->name);
    }
}
