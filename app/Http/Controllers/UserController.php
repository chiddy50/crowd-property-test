<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vedmant\FeedReader\Facades\FeedReader;
use Illuminate\Support\Facades\Http;
use App\Models\FeedList;
use Log;

class UserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // if(Auth::check()){

            $user = Auth::user();
            $feeds = $user->feeds;

            // dd($feeds);
            return view('welcome')->with([
                'feeds' => $feeds
            ]);

        // }

        // return redirect("login")->withSuccess('Please sign in or register');
    }

}
