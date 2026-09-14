<?php

declare(strict_types=1);

/**
 * Laravel-oriented: FormRequest as authorize-only adapter.
 * Doc extract: Example_laravel_form_request_adapter
 *
 * Illustrative — not executed against Laravel in this repo.
 */

namespace BristolianDatatypeExamples\Laravel;

use DataType\Basic\BasicString;
use DataType\Create\CreateFromArray;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

// Example_laravel_form_request_adapter start
class CreatePostParams implements DataType
{
    use CreateFromArray;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('title')]
        public readonly string $title,
        #[BasicString('body')]
        public readonly string $body,
    ) {
    }
}

/**
 * Sketch of a Laravel FormRequest that does not duplicate validation rules.
 * authorize() stays here; shape/validation live on CreatePostParams.
 */
class CreatePostRequest /* extends \Illuminate\Foundation\Http\FormRequest */
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // Intentionally empty: DataType owns validation.
        return [];
    }

    public function params(): CreatePostParams
    {
        // Prefer the PSR-7 path when available; array form is fine for docs:
        return CreatePostParams::createFromArray($this->all());
    }

    /** @return array<string, mixed> */
    private function all(): array
    {
        return [];
    }
}

// Controller: type-hint CreatePostRequest for auth, then $request->params().
// Example_laravel_form_request_adapter end
