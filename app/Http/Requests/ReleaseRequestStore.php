<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseRequestStore extends FormRequest
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
            'project' => 'bail|required',
            'version' => 'bail|required',
            'goal' => 'bail|required',
            'note' => 'bail|required',
            'release_date' => 'bail|required',
        ];
    }
}
