<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Tests;

use BristolianDatatypeExamples\Runnable\SearchDataType;
use DataType\Exception\Runtime\ValidationException;
use PHPUnit\Framework\TestCase;
use function BristolianDatatypeExamples\Runnable\queryRequest;

class SearchDataTypeTest extends TestCase
{
    public function testCreateFromRequest_reads_query_params(): void
    {
        $request = queryRequest([
            'search' => 'stairs',
            'limit' => '5',
        ]);

        $searchDataType = SearchDataType::createFromRequest($request);

        $this->assertSame('stairs', $searchDataType->search);
        $this->assertSame(5, $searchDataType->limit);
    }

    public function testCreateFromRequest_uses_default_limit(): void
    {
        $request = queryRequest([
            'search' => 'stairs',
        ]);

        $searchDataType = SearchDataType::createFromRequest($request);

        $this->assertSame(10, $searchDataType->limit);
    }

    public function testCreateFromRequest_throws_when_search_missing(): void
    {
        $this->expectException(ValidationException::class);

        SearchDataType::createFromRequest(queryRequest([]));
    }

    public function testCreateOrErrorFromRequest_returns_instance_when_valid(): void
    {
        $request = queryRequest([
            'search' => 'stairs',
        ]);

        [$searchDataType, $validationProblems] = SearchDataType::createOrErrorFromRequest($request);

        $this->assertSame([], $validationProblems);
        $this->assertInstanceOf(SearchDataType::class, $searchDataType);
        $this->assertSame('stairs', $searchDataType->search);
    }

    public function testCreateOrErrorFromRequest_returns_problems_when_invalid(): void
    {
        [$searchDataType, $validationProblems] = SearchDataType::createOrErrorFromRequest(
            queryRequest([])
        );

        $this->assertNotSame([], $validationProblems);
        $this->assertNotInstanceOf(SearchDataType::class, $searchDataType);
    }
}
