<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitConversionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method()){
            case 'POST':
                return [
                    'from_unit' => 'required',
                    'from_value' => 'required',
                    'to_unit' => 'required',
                    'to_value' => 'required',
                ];
                break;
            case 'GET':
                return[
                    'from_unit' => 'required',
                    'from_value' => 'required',
                    'to_value' => 'required',
                ];
                break;
        }
    }
}
