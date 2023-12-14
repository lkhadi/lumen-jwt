<?php

namespace App\Http\Requests;

use App\Utils\ValidationId;
use Pearl\RequestValidate\RequestAbstract;

class ExampleRequest extends RequestAbstract
{
    use ValidationId;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'example' => ['required']
        ];
    }

    public function attributes(): array
    {
        return [
            'example' => 'Example',
        ];
    }

    public function messages(): array
    {
        return $this->validationMessages;
    }
}
