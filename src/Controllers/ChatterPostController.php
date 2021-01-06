<?php

namespace SkyRaptor\Chatter\Controllers;

use Carbon\Carbon;
use SkyRaptor\Chatter\Events\ChatterAfterNewResponse;
use SkyRaptor\Chatter\Events\ChatterBeforeNewResponse;
use SkyRaptor\Chatter\Models\Models;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;

class ChatterPostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $stripped_tags_body = ['body' => strip_tags($request->body)];
        $validator = Validator::make($stripped_tags_body, [
            'body' => 'required|min:10',
        ],[
			'body.required' => trans('chatter::alert.danger.reason.content_required'),
			'body.min' => trans('chatter::alert.danger.reason.content_min'),
		]);

        Event::dispatch(new ChatterBeforeNewResponse($request, $validator));

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (config('chatter.security.limit_time_between_posts')) {
            if ($this->notEnoughTimeBetweenPosts()) {
                $minutes = trans_choice('chatter::messages.words.minutes', config('chatter.security.time_between_posts'));
                $chatter_alert = [
                    'chatter_alert_type' => 'danger',
                    'chatter_alert'      => trans('chatter::alert.danger.reason.prevent_spam', [
                                                'minutes' => $minutes,
                                            ]),
                    ];

                return back()->with($chatter_alert)->withInput();
            }
        }

        $request->request->add(['user_id' => Auth::user()->id]);
        $request->request->add(['markdown' => 1]);

        $new_post = Models::post()->create($request->all());

        $discussion = Models::discussion()->find($request->chatter_discussion_id);

        $category = Models::category()->find($discussion->chatter_category_id);
        if (!isset($category->slug)) {
            $category = Models::category()->first();
        }

        $chatter_alert = [
            'chatter_alert_type' => 'danger',
            'chatter_alert'      => trans('chatter::alert.danger.reason.trouble'),
        ];

        if ($new_post->exists) {
            $discussion->last_reply_at = $discussion->freshTimestamp();
            $discussion->save();
            
            Event::dispatch(new ChatterAfterNewResponse($request, $new_post));

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.submitted_to_post'),
            ];
        }

        return redirect(route('chatter.discussion.showInCategory', ['category' => $category->category->slug, 'slug' => $discussion->slug]))->with($chatter_alert);
    }

    private function notEnoughTimeBetweenPosts()
    {
        $user = Auth::user();

        $past = Carbon::now()->subMinutes(config('chatter.security.time_between_posts'));

        $last_post = Models::post()->where('user_id', '=', $user->id)->where('created_at', '>=', $past)->first();

        if (isset($last_post)) {
            return true;
        }

        return false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $stripped_tags_body = ['body' => strip_tags($request->body)];
        $validator = Validator::make($stripped_tags_body, [
            'body' => 'required|min:10',
        ],[
			'body.required' => trans('chatter::alert.danger.reason.content_required'),
			'body.min' => trans('chatter::alert.danger.reason.content_min'),
		]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $post = Models::post()->find($id);
        if (!Auth::guest() && (Auth::user()->id == $post->user_id)) {
            $post->body = $request->body;
            $post->save();

            $discussion = Models::discussion()->find($post->chatter_discussion_id);

            $category = Models::category()->find($discussion->chatter_category_id);
            if (!isset($category->slug)) {
                $category = Models::category()->first();
            }

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.updated_post'),
            ];

            return redirect(route('chatter.discussion.showInCategory', ['category' => $category->category->slug, 'slug' => $discussion->slug]))->with($chatter_alert);
        } else {
            $chatter_alert = [
                'chatter_alert_type' => 'danger',
                'chatter_alert'      => trans('chatter::alert.danger.reason.update_post'),
            ];

            return redirect(route('chatter.home'))->with($chatter_alert);
        }
    }

    /**
     * Delete post.
     *
     * @param string $id
     * @param  \Illuminate\Http\Request
     *
     * @return \Illuminate\Routing\Redirect
     */
    public function destroy($id, Request $request)
    {
        $post = Models::post()->with('discussion')->findOrFail($id);

        if ($request->user()->id !== (int) $post->user_id) {
            return redirect(route('chatter.home'))->with([
                'chatter_alert_type' => 'danger',
                'chatter_alert'      => trans('chatter::alert.danger.reason.destroy_post'),
            ]);
        }

        if ($post->discussion->posts()->oldest()->first()->id === $post->id) {
            if(config('chatter.soft_deletes')) {
                $post->discussion->posts()->delete();
                $post->discussion()->delete();
            } else {
                $post->discussion->posts()->forceDelete();
                $post->discussion()->forceDelete();
            }

            return redirect(route('chatter.home'))->with([
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.destroy_post'),
            ]);
        }

        $post->delete();

        $url = route('chatter.discussion.showInCategory', ['category' => $post->discussion->category->slug, 'slug' => $post->discussion->slug]);

        return redirect($url)->with([
            'chatter_alert_type' => 'success',
            'chatter_alert'      => trans('chatter::alert.success.reason.destroy_from_discussion'),
        ]);
    }
}
