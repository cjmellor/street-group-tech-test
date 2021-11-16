<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReadCsvRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'csv_data' => 'required|file|mimes:csv,txt|max:3000'
        ];
    }
}
