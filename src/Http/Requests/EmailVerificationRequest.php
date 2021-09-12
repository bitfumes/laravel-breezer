<?php

namespace Bitfumes\Breezer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailVerificationRequest extends FormRequest
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
        return [
            //
        ];
    }

    public function validate()
    {
        if (!$user = $this->userAvailable()) {
            return false;
        }

        if (!$this->verifySignature($user)) {
            return false;
        }

        return $user;
    }

    public function userAvailable()
    {
        $userModel = app()['config']['breezer.models.user'];
        return $userModel::find($this->route('id'));
    }

    public function verifySignature($user)
    {
        $sign = sha1($this->expires . $user->getEmailForVerification());
        if (now()->timestamp < $this->expires && $sign === $this->signature) {
            return true;
        }

        return false;
    }
}
