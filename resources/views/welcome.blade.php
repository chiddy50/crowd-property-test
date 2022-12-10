
@extends('layouts.auth')

@section('content')

<div class="">
    <div class="px-6 py-4">
        <h4 style="font-size: 3rem; color: gray;" class="mb-4">Welcome Back</h4>

        <h3 style="margin: 2rem 0 1rem;">Here is your feed list: </h3>
        @foreach($feeds as $feed)
            <div class="p-6" style="margin: 0 0 5px; border: 1px solid #ddd; border-radius: 1rem; ">
                <p class="" style="font-size: 2rem;">{{ $feed->title }}</p>
                <p>{{ $feed->feed_url }}</p>
                <p class="text-sm text-gray-400">Story Count: {{ $feed->story_count }}</p>
            </div>
        @endforeach
    </div>

</div>
@endsection
