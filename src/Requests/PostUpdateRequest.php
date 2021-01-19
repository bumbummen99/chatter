<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class PostUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (App::environment('testing')) {
            /* Example, deal with it in a middleware */
            return !Auth::guest() && (Auth::user()->id == $this->route->post->user_id);
        } else {
            return true;
        }
        
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        $this->redirect(route('chatter.home'))->with([
            'chatter_alert_type' => 'danger',
            'chatter_alert'      => trans('chatter::alert.danger.reason.update_post'),
        ]);
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
            'body'                  => 'required|min:10',
            'body_content'          => 'required|min:10',
            'chatter_discussion_id' => 'required|integer|exists:chatter_discussion,id',
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
			'body.required' => trans('chatter::alert.danger.reason.content_required'),
			'body.min' => trans('chatter::alert.danger.reason.content_min'),
		];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        $data = $this->validator->validated();

        Arr::set($data, 'user_id', Auth::user()->id);

        Arr::set($data, 'markdown', true);

        return $data;
    }
}
