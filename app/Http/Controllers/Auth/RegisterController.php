<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FeedList;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Log;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request){
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request){
        $validator = $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if($validator) {
            $user = User::create([
                'email' => $validator['email'],
                'password' => Hash::make($validator['password'])
            ]);

            $random_feed = $this->getRandomFeed();

            $feed = FeedList::create([
                'user_id' => $user->id,
                'feed_url' => $random_feed,
                'public_id' => Str::uuid()
            ]);

            $this->getFeedData($random_feed);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                return redirect()->intended('/')->withSuccess('Signed in');
            }

        }else{
            return back()->with('error', 'There was an error.');
        }
    }

    private function getFeedData($feed_url){
        try {

            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
            ])->get($feed_url);

            if ($response->status() == 200) {

                $xml = simplexml_load_string($response);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                // $array = collect($xml);

                $feed = FeedList::where('feed_url', $feed_url)->first();
                $feed->title = $array['channel']['title'] ?? null;
                $feed->story_count = count($array['channel']['item']) ?? null;
                $feed->save();

                return [ 'error' => false];
            }else{
                return [ 'error' => true];
            }

        }catch (\Exception $e) {
            Log::error('Error fetching RSS feed: ' . $e->getMessage());
            return [ 'error' => true];
        }
    }

    private function getRandomFeed(){
        $rss_feeds = [
            'https://www.theguardian.com/world/rss',
            'https://www.nasa.gov/rss/dyn/breaking_news.rss',
            'http://www.independent.co.uk/news/uk/rss',
            'http://feeds.feedburner.com/daily-express-news-showbiz',
            'https://www.huffpost.com/section/world-news/feed',
            'http://feeds.foxnews.com/foxnews/latest'
        ];

        $feeds_count = count($rss_feeds)- 1;
        for ($feed = 0; $feed < $feeds_count; $feed++) {
            $n = rand(0, $feeds_count);
            $random_url = $rss_feeds[$n];
        }
        return $random_url;
    }

    /**
     * This private method can also  be used to generate 2 random urls for
     * and a job can be dispatched to fetch data
     */
    private function generateTwoRandomFeed(){
        $rss_feeds = [
            'https://www.theguardian.com/world/rss',
            'https://www.nasa.gov/rss/dyn/breaking_news.rss',
            'http://www.independent.co.uk/news/uk/rss',
            'http://feeds.feedburner.com/daily-express-news-showbiz',
            'https://www.huffpost.com/section/world-news/feed',
            'http://feeds.foxnews.com/foxnews/latest'
        ];
        function generateTwoFeeds($rss_feeds){
            $feeds_count = count($rss_feeds)- 1;
            for ($feed = 0; $feed < $feeds_count; $feed++) {
                $n = rand(0, $feeds_count);
            }
            return $n;
        }
        $rand1 = generateTwoFeeds($rss_feeds);
        $rand2 = generateTwoFeeds($rss_feeds);

        do {
            $rand2 = generateTwoFeeds($rss_feeds);
        } while ($rand1 == $rand2);

        return [
            'feed_one' => $rss_feeds[$rand1],
            'feed_two' => $rss_feeds[$rand2]
        ];
    }
}
