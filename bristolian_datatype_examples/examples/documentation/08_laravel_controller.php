<?php

declare(strict_types=1);

/**
 * Laravel-oriented: construct a DataType in the controller.
 * Doc extract: Example_laravel_controller
 */

namespace BristolianDatatypeExamples\Laravel;

use DataType\Basic\BasicString;
use DataType\Basic\OptionalBasicString;
use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\Exception\Runtime\ValidationException;
use DataType\GetInputTypesFromAttributes;
use Psr\Http\Message\ServerRequestInterface;

// Example_laravel_controller start
class UpdateProfileParams implements DataType
{
    use CreateFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('display_name')]
        public readonly string $display_name,
        #[OptionalBasicString('bio')]
        public readonly string|null $bio,
    ) {
    }
}

class ProfileController
{
    /** @return array<string, mixed> */
    public function update(ServerRequestInterface $request): array
    {
        try {
            $params = UpdateProfileParams::createFromRequest($request);
        }
        catch (ValidationException $exception) {
            // Map $exception->getValidationProblems() to your JSON error shape.
            throw $exception;
        }

        // Use $params->display_name, $params->bio — already typed and validated.
        return ['ok' => true];
    }
}
// Example_laravel_controller end
