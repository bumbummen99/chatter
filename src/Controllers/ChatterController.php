<?php

namespace SkyRaptor\Chatter\Controllers;

use Illuminate\Support\Facades\Auth;
use SkyRaptor\Chatter\Models\Models;
use Illuminate\Routing\Controller;

class ChatterController extends Controller
{
    public function index($slug = '')
    {
        /* Build the basic query for all discussions, ordered and including users posts and categories */        
        $discussions = Models::discussion()->with('user')->with('post')->with('category')->orderBy(config('chatter.order_by.discussions.order'), config('chatter.order_by.discussions.by'));

        /* Initialize an empty variable for the category, will be Category or NULL */
        $category = null;

        /* Check if the slug is provided i.e. we are in a specific Category */
        if (isset($slug)) {
            /* Try to find the Category by the provided */
            $category = Models::category()->where('slug', '=', $slug)->firstOrFail();
            
            /* Scope the Discussion query to the Category */
            $discussions = $discussions->where('chatter_category_id', '=', $category->id);
        }

        /* Query the Discussions */        
        $discussions = $discussions->paginate(config('chatter.paginate.num_of_results'));
        
        return view('chatter::home', [
            'discussions' => $discussions,
            'categories' => Models::category()->get(),
            'current_category_id' => $category ? $category->id : null,
        ]);
    }
    
    public function login()
    {
        if (!Auth::check()) {
            return redirect()->route(config('chatter.routes.login'), [
                'redirect' => config('chatter.routes.home'),
            ])->with('flash_message', 'Please create an account before posting.');
        }
    }
    
    public function register()
    {
        if (!Auth::check()) {
            return redirect()->route(config('chatter.routes.register'), [
                'redirect' => config('chatter.routes.home')
            ])->with('flash_message', 'Please register for an account.');
        }
    }
}
