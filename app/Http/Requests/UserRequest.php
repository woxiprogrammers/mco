<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                    'password' => 'required|min:6|max:20',
                ];
                break;

            case 'PUT':
                return [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                ];
                break;
        }
    }
}
