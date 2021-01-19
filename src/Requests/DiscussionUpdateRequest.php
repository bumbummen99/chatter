<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class DiscussionUpdateRequest extends FormRequest
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
            return !Auth::guest() && (Auth::user()->id == $this->route->discussion->user_id);
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
            'chatter_alert'      => trans('chatter::alert.danger.reason.update_disucssion'),
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
            'title' => 'required|min:5|max:255',
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
            ]
        ];
    }
}
