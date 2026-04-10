<?php

namespace App\Http\Requests\Master;

use App\Domain\Master\Enums\MasterStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category');

        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($id)],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', Rule::enum(MasterStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A category with this name already exists.',
            'status'      => 'Status must be one of: active, inactive.',
        ];
    }
}
