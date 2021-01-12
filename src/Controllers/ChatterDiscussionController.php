<?php

namespace SkyRaptor\Chatter\Controllers;

use App\Http\Requests\DiscussionStoreRequest;
use App\Http\Requests\DiscussionUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Carbon\Carbon;
use SkyRaptor\Chatter\Events\ChatterAfterNewDiscussion;
use SkyRaptor\Chatter\Events\ChatterBeforeNewDiscussion;
use SkyRaptor\Chatter\Models\Models;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use SkyRaptor\Chatter\Models\Category;
use SkyRaptor\Chatter\Models\Discussion;

class ChatterDiscussionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(DiscussionStoreRequest $request)
    {
        /* Prevent new discussion Spam (if configured) */
        $this->checkTimeBetweenDiscussions();

        /* Dispatch the Event to inform that a new Discussion is about to be created */
        Event::dispatch(new ChatterBeforeNewDiscussion());

        /* Get the validated Request data */
        $data = $request->validated();

        /* Generate an URL friendly slug from the Discussion's title */
        $slug = $this->getUniqueDiscussionSlug(Arr::get($data, 'title'));

        /* Get the parent Category if set */
        $category = Models::category()->find(Arr::get($data, 'chatter_category_id'));
        if (!isset($category->slug)) {
            $category = Models::category()->first();
        }

        /* Create the Discussion */
        $discussion = Models::discussion()->create([
            'title'               => Arr::get($data, 'title'),
            'chatter_category_id' => Arr::get($data, 'chatter_category_id'),
            'user_id'             => Auth::user()->id,
            'slug'                => $slug,
            'color'               => Arr::get($data, 'color'),
            'last_reply_at'       => Carbon::now(),
        ]);

        /* Create the Post */
        $post = Models::post()->create([
            'chatter_discussion_id' => $discussion->id,
            'user_id'               => Auth::user()->id,
            'body'                  => Arr::get($data, 'body'),
            'markdown'              => 1,
        ]);

        $chatter_alert = [
            'chatter_alert_type' => 'danger',
            'chatter_alert'      => trans('chatter::alert.danger.reason.create_discussion'),
        ];

        if ($post->id) {
            Event::dispatch(new ChatterAfterNewDiscussion($discussion, $post));

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.created_discussion'),
            ];
        }

        return redirect(route('chatter.discussion.showInCategory', [
            'category' => $discussion->category, 
            'discussion' => $discussion
        ]))->with($chatter_alert);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category, string $slug)
    {
        /* Try to find the Discussion in the Category */
        $discussion = $category->discussions()->where('slug', '=', $slug)->firstOrFail();

        /* Increment the discussions views */
        $discussion->increment('views');
        
        return view('chatter::discussion', [
            'discussion' => $discussion,
            'posts' => $discussion->posts()->orderBy(config('chatter.order_by.posts.order'), config('chatter.order_by.posts.by'))->paginate(10),
        ]);
    }

    /**
     * TODO
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(DiscussionUpdateRequest $request, Discussion $discussion)
    {
        /* Update the Discussion */
        $discussion->update($request->validated());

        /* Show the updated Discussio */
        return redirect(route('chatter.discussion.showInCategory', [
            'category' => $discussion->category, 
            'discussion' => $discussion
        ]))->with([
            'chatter_alert_type' => 'success',
            'chatter_alert'      => trans('chatter::alert.success.reason.updated_discussion'),
        ]);
    }

    private function getUniqueDiscussionSlug(string $name) : string
    {
        $index = 0;
        $unique = false;

        $slug = null;

        while (!$unique) {
            /* Generate an URL friendly slug */
            $slug = Str::slug($name, '-');

            /* If we have an index increase and append it */
            if ($index > 0) {
                $slug .= '-' . $index;
            }

            /* Verify that the slug is unique */
            $unique = !Models::discussion()->where('slug', '=', $slug)->withTrashed()->first();

            /* Increment the Index if no unique slug has been found */
            if (!$unique) {
                $index++;
            }
        }

        return $slug;
    }

    private function checkTimeBetweenDiscussions()
    {
        if (config('chatter.security.limit_time_between_posts')) {
            if ($this->notEnoughTimeBetweenDiscussion()) {
                $minutes = trans_choice('chatter::messages.words.minutes', config('chatter.security.time_between_posts'));
                $chatter_alert = [
                    'chatter_alert_type' => 'danger',
                    'chatter_alert'      => trans('chatter::alert.danger.reason.prevent_spam', [
                        'minutes' => $minutes,
                    ]),
                ];

                return redirect()->route('chatter.home')->with($chatter_alert)->withInput();
            }
        }
    }

    private function notEnoughTimeBetweenDiscussion()
    {
        $past = Carbon::now()->subMinutes(config('chatter.security.time_between_posts'));

        return !!Models::discussion()->where('user_id', '=', Auth::user()->id)->where('created_at', '>=', $past)->first();
    }
}
