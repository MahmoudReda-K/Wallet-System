<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'receiver' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Get the validation data that should be used for validating the request.
     */
    public function validationData(): array
    {
        return array_merge($this->all(), [
            'description' => $this->input('description', null),
        ]);
    }
}
