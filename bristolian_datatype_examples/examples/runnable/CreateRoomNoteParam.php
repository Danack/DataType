<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use DataType\Basic\OptionalBasicString;
use DataType\Create\CreateFromArray;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

class CreateRoomNoteParam implements DataType
{
    use CreateFromArray;
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
