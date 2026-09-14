<?php

declare(strict_types=1);

/**
 * Bristolian-style: JSON body → DataType in a controller action.
 * Doc extract: Example_bristolian_json_body
 *
 * Modelled on Rooms::addNote / CreateRoomNoteParam (simplified).
 */

namespace BristolianDatatypeExamples\Bristolian;

use DataType\Basic\OptionalBasicString;
use DataType\Create\CreateFromArray;
use DataType\Create\CreateFromRequest;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use Psr\Http\Message\ServerRequestInterface;

// Example_bristolian_json_body start
class CreateRoomNoteParam implements DataType
{
    use CreateFromArray;
    use CreateFromRequest;
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[RoomNoteTitle('title')]
        public readonly string $title,
        #[RoomNoteMarkdown('markdown')]
        public readonly string $markdown,
        #[OptionalBasicString('document_timestamp')]
        public readonly string|null $document_timestamp,
    ) {
    }
}

// In the controller: decode JSON, then construct.
// $jsonInput->getData() is array<string, mixed> from the request body.
/** @param array<string, mixed> $jsonBody */
function rooms_add_note(array $jsonBody): CreateRoomNoteParam
{
    return CreateRoomNoteParam::createFromArray($jsonBody);
}

// Or from a PSR-7 request (query / parsed body via Psr7VarMap):
function rooms_add_note_from_request(ServerRequestInterface $request): CreateRoomNoteParam
{
    return CreateRoomNoteParam::createFromRequest($request);
}
// Example_bristolian_json_body end
