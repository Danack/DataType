<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Tests;

use BristolianDatatypeExamples\Runnable\BristolStairsGpsParams;
use PHPUnit\Framework\TestCase;
use VarMap\ArrayVarMap;

class BristolStairsGpsParamsTest extends TestCase
{
    public function testCreateFromVarMap_allows_missing_coordinates(): void
    {
        $params = BristolStairsGpsParams::createFromVarMap(new ArrayVarMap([]));

        $this->assertNull($params->latitude);
        $this->assertNull($params->longitude);
    }

    public function testCreateFromVarMap_reads_coordinates(): void
    {
        $params = BristolStairsGpsParams::createFromVarMap(new ArrayVarMap([
            'gps_latitude' => '51.4545',
            'gps_longitude' => '-2.5879',
        ]));

        $this->assertSame(51.4545, $params->latitude);
        $this->assertSame(-2.5879, $params->longitude);
    }
}
