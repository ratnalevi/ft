<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PosItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'POSItemsID' => 'required',
            'AccountID' => 'required',
            'LocationID' => 'required',
            'BeerBrandID' => 'required',
            'BeerTypeID' => 'required',
            'Ounces' => 'required',
            'ItemNUM' => 'required|string',
            'ItemName' => 'required|string',
            'ItemDESC' => 'required|string',
            'ItemFLAG' => 'required',
            'RecordStatus' => 'required',
            'InsertDateTime' => 'required|string',
            'UpdateDateTime' => 'required|string',
        ];
    }
}
