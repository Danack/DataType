<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Tests;

use BristolianDatatypeExamples\Runnable\SearchDataTypeWithRules;
use DataType\Exception\Runtime\ValidationException;
use PHPUnit\Framework\TestCase;
use function BristolianDatatypeExamples\Runnable\queryRequest;

class SearchDataTypeWithRulesTest extends TestCase
{
    public function testCreateFromRequest_trims_and_accepts_long_enough_term(): void
    {
        $request = queryRequest([
            'search' => '  abc  ',
        ]);

        $searchDataType = SearchDataTypeWithRules::createFromRequest($request);

        $this->assertSame('abc', $searchDataType->search);
    }

    public function testCreateFromRequest_rejects_short_term(): void
    {
        $this->expectException(ValidationException::class);

        SearchDataTypeWithRules::createFromRequest(queryRequest([
            'search' => 'ab',
        ]));
    }
}
