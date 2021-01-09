<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscussionStoreRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'body_content' => strip_tags($this->body),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'               => 'required|min:5|max:255',
            'body_content'        => 'required|min:10',
            'chatter_category_id' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
			'title.required' =>  trans('chatter::alert.danger.reason.title_required'),
			'title.min'     => [
				'string'  => trans('chatter::alert.danger.reason.title_min'),
			],
			'title.max' => [
				'string'  => trans('chatter::alert.danger.reason.title_max'),
			],
			'body_content.required' => trans('chatter::alert.danger.reason.content_required'),
			'body_content.min' => trans('chatter::alert.danger.reason.content_min'),
			'chatter_category_id.required' => trans('chatter::alert.danger.reason.category_required'),
		];
    }
}
