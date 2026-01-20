@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body" style="height: 80vh; padding: 0;">
            <!-- Embed Heyzine Flipbook -->
            <iframe 
                allowfullscreen="allowfullscreen" 
                scrolling="no" 
                class="fp-iframe" 
                src="https://heyzine.com/flip-book/dce36e099f.html" 
                style="border: 1px solid lightgray; width: 100%; height: 100%;">
            </iframe>
        </div>
    </div>
</div>
@endsection
