<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Rules\NoCircularCategories;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->id;
        return [
            'name' => [
                'required',
                Rule::unique('categories', 'name')->whereNot('id', $id)
            ],
            'parent_id' => [new NoCircularCategories(($this->id))]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'name already exist, please choose new name',
        ];
    }
}
