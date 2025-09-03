<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdersFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from'       => ['nullable', 'date'],
            'to'         => ['nullable', 'date'],
            'status'     => ['nullable', 'string'],
            'email'      => ['nullable', 'email'],
            'min_total'  => ['nullable', 'numeric'],
            'max_total'  => ['nullable', 'numeric'],
            'page'       => ['nullable', 'integer', 'min:1'],
            'limit'      => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return $this->validated();
    }
}
