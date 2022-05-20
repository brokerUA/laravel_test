<?php

namespace App\Http\Requests;

use App\Rules\EmailRFC2822;
use Illuminate\Foundation\Http\FormRequest;

class ApiStoreUserRequest extends FormRequest
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
            'name' => 'required|min:2|max:60',
            'email' => [
                'required',
                'email:rfc',
                new EmailRFC2822
            ],
            'phone' => 'required|regex:/^[\+]{0,1}380([0-9]{9})$/',
            'position_id' => 'bail|required|int|exists:App\Models\Position,id',
            'photo' => 'required|image|max:5120|dimensions:min_width=70,min_height=70|mimes:jpg',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void|\Illuminate\Http\JsonResponse
     */
    public function withValidator($validator)
    {
        if ($validator->fails()) {

            $result = collect([
                'success' => false,
                'message' => 'Validation failed',
                'fails' => collect([])
            ]);

            $errors = $validator->errors();

            foreach ($errors->keys() as $key) {
                $result->get('fails')
                    ->put($key, $errors->get($key));
            }

            return response()->json($result, '422');
        }
    }
}
