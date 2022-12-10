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
        $user = Auth::user();
        $feeds = $user->feeds;

        return view('welcome')->with([
            'feeds' => $feeds
        ]);
    }

    /**
     * Get feed details
     */
    public function feed($id)
    {
        $user = Auth::user();
        $feed = FeedList::where('public_id', $id)->where('user_id', $user->id)->first();

        if (!$feed) {
            return view('feed')->with([
                'error' => true,
                'message' => 'Feed URL not found'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
            ])->get($feed->feed_url);

            if ($response->status() == 200) {

                $xml = simplexml_load_string($response);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);

                $title = $array['channel']['title'] ?? 'Default title';
                $stories = $array['channel']['item'] ?? [];
                return [
                    'title' => $title,
                    'stories' => $stories,
                ];

                return view('feed')->with([
                    'title' => $title,
                    'stories' => $stories,
                ]);
            }else{
                return [ 'error' => true];
            }

        }catch (\Exception $e) {
            Log::error('Error fetching RSS feed: ' . $e->getMessage());
            // return [ 'error' => true];
        }


    }

}
