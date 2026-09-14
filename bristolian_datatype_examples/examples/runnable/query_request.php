<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @param array<string, string> $query
 */
function queryRequest(array $query): ServerRequestInterface
{
    return (new ServerRequest())->withQueryParams($query);
}
