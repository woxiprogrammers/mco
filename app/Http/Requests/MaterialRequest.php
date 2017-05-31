<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
                    'category_name' => 'required',
                    'rate_per_unit' => 'required',
                    'unit' => 'required',
                ];
                break;
            case 'GET':
                return[
                    'name' => 'required',
                    'category_name' => 'required',
                    'rate_per_unit' => 'required',
                    'unit' => 'required',
                ];
                break;
        }
    }
}
