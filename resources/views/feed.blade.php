@extends('layouts.auth')

@section('content')
<div class="">
    <div class="px-6 py-4">
        <h4 style="font-size: 3rem; color: gray;margin-bottom: 1rem;" class="mb-4">
            {{ $title }}
        </h4>

        @foreach($stories as $story)
            <div class="p-6 feed-item" style="margin: 0 0 20px; border-bottom: 1px solid #ddd;">
                <p class="font-bold" style="font-size:1.2rem;margin-bottom: 1rem;font-weight: bold;">
                    {{ $story['title'] }}
                </p>
                <p class="text-sm text-gray-400">{{ $story['description'] }}</p>
            </div>
        @endforeach
    </div>

</div>

@endsection
