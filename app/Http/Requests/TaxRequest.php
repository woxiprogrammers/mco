<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
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
                    'name' => 'required',
                    'base_percentage' => 'required',
                ];
                break;
            case 'GET':
                return[
                    'name' => 'required',
                    'base_percentage' => 'required',
                ];
                break;
        }
    }
}
