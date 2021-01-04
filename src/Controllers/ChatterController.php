<?php

namespace SkyRaptor\Chatter\Controllers;

use Illuminate\Support\Facades\Auth;
use SkyRaptor\Chatter\Models\Models;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;

class ChatterController extends Controller
{
    public function index($slug = '')
    {
        $pagination_results = config('chatter.paginate.num_of_results');
        
        $discussions = Models::discussion()->with('user')->with('post')->with('postsCount')->with('category')->orderBy(config('chatter.order_by.discussions.order'), config('chatter.order_by.discussions.by'));
        if (isset($slug)) {
            $category = Models::category()->where('slug', '=', $slug)->first();
            
            if (isset($category->id)) {
                $current_category_id = $category->id;
                $discussions = $discussions->where('chatter_category_id', '=', $category->id);
            } else {
                $current_category_id = null;
            }
        }
        
        $discussions = $discussions->paginate($pagination_results);
        
        $chatter_editor = config('chatter.editor');
        
        if ($chatter_editor == 'simplemde') {
            // Dynamically register markdown service provider
            App::register('GrahamCampbell\Markdown\MarkdownServiceProvider');
        }
        
        return view('chatter::home', [
            'discussions' => $discussions,
            'categories' => Models::category()->get(),
            'chatter_editor' => $chatter_editor,
            'current_category_id' => $current_category_id,
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
