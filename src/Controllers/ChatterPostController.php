<?php

namespace SkyRaptor\Chatter\Controllers;

use App\Http\Requests\PostDestroyRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use Carbon\Carbon;
use SkyRaptor\Chatter\Events\ChatterAfterNewResponse;
use SkyRaptor\Chatter\Events\ChatterBeforeNewResponse;
use SkyRaptor\Chatter\Models\Models;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use SkyRaptor\Chatter\Models\Discussion;
use SkyRaptor\Chatter\Models\Post;

class ChatterPostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(PostStoreRequest $request)
    {       
        /** @var Discussion */
        $discussion = Discussion::find($request->chatter_discussion_id);

        Event::dispatch(new ChatterBeforeNewResponse($discussion));

        /* Prevent Post spam (if configured */
        $this->checkTimeBetweenPosts();

        /* Create the Post associated to the Discussion */
        $post = $discussion->posts()->create($request->validated());

        $chatter_alert = [
            'chatter_alert_type' => 'danger',
            'chatter_alert'      => trans('chatter::alert.danger.reason.trouble'),
        ];

        if ($post->exists) {
            /* Update the discussions last_reply */
            $discussion->last_reply_at = $discussion->freshTimestamp();
            $discussion->save();
            
            /* Dispatch the Event to inform the system of the new response */
            Event::dispatch(new ChatterAfterNewResponse($discussion, $post));

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.submitted_to_post'),
            ];
        }

        return redirect(route('chatter.discussion.showInCategory', [
            'category' => $discussion->category,
            'slug' => $discussion->slug,
        ]))->with($chatter_alert);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $post->body = Arr::get($request->validated(), 'body');
        $post->save();

        return redirect(route('chatter.discussion.showInCategory', [
            'category' => $post->discussion->category->slug,
            'slug' => $post->discussion->slug
        ]))->with([
            'chatter_alert_type' => 'success',
            'chatter_alert'      => trans('chatter::alert.success.reason.updated_post'),
        ]);
    }

    /**
     * Delete post.
     *
     * @return \Illuminate\Routing\Redirect
     */
    public function destroy(PostDestroyRequest $request, Post $post)
    {
        /* Check if it is the first / oldest post of the disussion */
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
        } else {
            /* Normally delete the Post */
            $post->delete();

            return redirect(route('chatter.discussion.showInCategory', [
                'category' => $post->discussion->category->slug, 
                'slug' => $post->discussion->slug
            ]))->with([
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.destroy_from_discussion'),
            ]);
        }        
    }

    private function checkTimeBetweenPosts()
    {
        if (config('chatter.security.limit_time_between_posts')) {

            $user = Auth::user();

            $past = Carbon::now()->subMinutes(config('chatter.security.time_between_posts'));

            $last_post = Models::post()->where('user_id', '=', $user->id)->where('created_at', '>=', $past)->first();

            if (isset($last_post)) {
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
    }
}
