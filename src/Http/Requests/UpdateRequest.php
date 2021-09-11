<?php

namespace Bitfumes\Breezer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $rules = [
            'name'     => 'min:3|max:25',
            'email'    => 'email|unique:users,email,' . auth()->id(),
        ];
        $custom = config('breezer.validations');
        $custom = gettype($custom) === 'array' ? $custom : $custom();
        return array_merge($rules, $custom);
    }
}
