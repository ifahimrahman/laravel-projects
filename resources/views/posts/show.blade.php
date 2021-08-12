@extends('layout')


@section('content')

<div class="row">
    <div class="col-8">
        @if($post->image)
        <div style="background-image: url('{{ $post->image->url() }}'); min-height: 500px; color: white; text-align: center; background-attachment: fixed;">
            <h1 style="padding-top: 100px; text-shadow: 1px 2px #000">
        @else
            <h1>
        @endif
            {{ $post->title }}
           @if(['show' => now()->diffInMinutes($post->created_at) < 30])
                Brand new Post!
            @endif
        @if($post->image)    
            </h1>
        </div>
        @else
            </h1>
        @endif
    </div>
</div>

<p>{{ $post->content}}</p>
<p>Created at {{ $post->created_at->diffForHumans() }}</p>

@if ((new Carbon\Carbon())->diffInMinutes($post->created_at)<5)
<strong>New!</strong>
@endif

<h4>Comments</h4>

@include('comments._form')

    @forelse($post->comments as $comment)
        <p>
            {{ $comment->content }} by {{ $comment->user->name ?? 'Anonymous'}}
        </p>
        <p class="text-muted">
            added {{ $comment->created_at->diffForHumans() }}
        </p>
    @empty
        <p>No comments yet!</p>
    @endforelse

@endsection('content')
