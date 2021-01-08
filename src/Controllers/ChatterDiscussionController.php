<?php

namespace SkyRaptor\Chatter\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Carbon\Carbon;
use SkyRaptor\Chatter\Events\ChatterAfterNewDiscussion;
use SkyRaptor\Chatter\Events\ChatterBeforeNewDiscussion;
use SkyRaptor\Chatter\Models\Models;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatterDiscussionController extends Controller
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
        $request->request->add(['body_content' => strip_tags($request->body)]);

        $validator = Validator::make($request->all(), [
            'title'               => 'required|min:5|max:255',
            'body_content'        => 'required|min:10',
            'chatter_category_id' => 'required',
         ],[
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
		]);
        

        Event::dispatch(new ChatterBeforeNewDiscussion($request, $validator));

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user_id = Auth::user()->id;

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

        // *** Let's gaurantee that we always have a generic slug *** //
        $slug = Str::slug($request->title, '-');

        $discussion_exists = Models::discussion()->where('slug', '=', $slug)->withTrashed()->first();
        $incrementer = 1;
        $new_slug = $slug;
        while (isset($discussion_exists->id)) {
            $new_slug = $slug.'-'.$incrementer;
            $discussion_exists = Models::discussion()->where('slug', '=', $new_slug)->withTrashed()->first();
            $incrementer += 1;
        }

        if ($slug != $new_slug) {
            $slug = $new_slug;
        }

        $new_discussion = [
            'title'               => $request->title,
            'chatter_category_id' => $request->chatter_category_id,
            'user_id'             => $user_id,
            'slug'                => $slug,
            'color'               => $request->color,
            'last_reply_at'       => Carbon::now(),
        ];

        $category = Models::category()->find($request->chatter_category_id);
        if (!isset($category->slug)) {
            $category = Models::category()->first();
        }

        $discussion = Models::discussion()->create($new_discussion);

        $new_post = [
            'chatter_discussion_id' => $discussion->id,
            'user_id'               => $user_id,
            'body'                  => $request->body,
            'markdown'              => 1,
        ];

        // add the user to automatically be notified when new posts are submitted
        $discussion->users()->attach($user_id);

        $post = Models::post()->create($new_post);

        $chatter_alert = [
            'chatter_alert_type' => 'danger',
            'chatter_alert'      => trans('chatter::alert.danger.reason.create_discussion'),
        ];

        if ($post->id) {
            Event::dispatch(new ChatterAfterNewDiscussion($request, $discussion, $post));

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.created_discussion'),
            ];
        }

        return redirect(route('chatter.discussion.showInCategory', ['category' => $discussion->category->slug, 'slug' => $slug]))->with($chatter_alert);
    }

    private function notEnoughTimeBetweenDiscussion()
    {
        $user = Auth::user();

        $past = Carbon::now()->subMinutes(config('chatter.security.time_between_posts'));

        $last_discussion = Models::discussion()->where('user_id', '=', $user->id)->where('created_at', '>=', $past)->first();

        if (isset($last_discussion)) {
            return true;
        }

        return false;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($category, $slug = null)
    {
        if (!isset($category) || !isset($slug)) {
            return redirect(route('chatter.home'));
        }

        /* Try to find the Discussion */
        $discussion = Models::discussion()->where('slug', '=', $slug)->first();
        if (is_null($discussion)) {
            abort(404);
        }

        /* Try to get the Category */
        $discussionCategory = $discussion->category;
        if ($category != $discussionCategory->slug) {
            return redirect(route('chatter.discussion.showInCategory', ['category' => $discussionCategory->category->slug, 'slug' => $discussion->slug]));
        }

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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * TODO
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function sanitizeContent($content)
    {
        libxml_use_internal_errors(true);
        // create a new DomDocument object
        $doc = new \DOMDocument();

        // load the HTML into the DomDocument object (this would be your source HTML)
        $doc->loadHTML($content);

        $this->removeElementsByTagName('script', $doc);
        $this->removeElementsByTagName('style', $doc);
        $this->removeElementsByTagName('link', $doc);

        // output cleaned html
        return $doc->saveHtml();
    }

    private function removeElementsByTagName($tagName, $document)
    {
        $nodeList = $document->getElementsByTagName($tagName);
        for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0;) {
            $node = $nodeList->item($nodeIdx);
            $node->parentNode->removeChild($node);
        }
    }
}
