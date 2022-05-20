<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiIndexUserRequest extends FormRequest
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
            'count' => 'int|min:1|max:100',
            'offset' => 'int|min:0',
            'page' => 'int|min:1', // FIXME Why "|exclude_with:offset" get error?
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

            return response()->json($result, 422);
        }
    }

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;
}
