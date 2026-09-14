<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Tests;

use BristolianDatatypeExamples\Runnable\CreateRoomNoteParam;
use DataType\Exception\Runtime\ValidationException;
use PHPUnit\Framework\TestCase;

class CreateRoomNoteParamTest extends TestCase
{
    public function testCreateFromArray_reads_fields(): void
    {
        $params = CreateRoomNoteParam::createFromArray([
            'title' => 'Agenda',
            'markdown' => '# Hello',
        ]);

        $this->assertSame('Agenda', $params->title);
        $this->assertSame('# Hello', $params->markdown);
        $this->assertNull($params->document_timestamp);
    }

    public function testCreateFromArray_rejects_empty_title(): void
    {
        $this->expectException(ValidationException::class);

        CreateRoomNoteParam::createFromArray([
            'title' => '',
            'markdown' => '# Hello',
        ]);
    }
}
