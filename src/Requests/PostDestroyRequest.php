<?php

namespace SkyRaptor\Chatter\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class PostDestroyRequest extends FormRequest
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
            'chatter_alert'      => trans('chatter::alert.danger.reason.destroy_post'),
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
            //
        ];
    }
}
