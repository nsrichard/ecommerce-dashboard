<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductsFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q'          => ['nullable', 'string'],
            'min_price'  => ['nullable', 'numeric'],
            'max_price'  => ['nullable', 'numeric'],
            'currency'   => ['nullable', 'string', 'size:3'],
            'page'       => ['nullable', 'integer', 'min:1'],
            'limit'      => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return $this->validated();
    }
}
