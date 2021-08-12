@extends('layout')

@section('content')

<form method="POST" action="{{route('posts.update',['post'=>$post->id])}}" enctype="multipart/form-data" >
@csrf
@method('PUT')

@include('posts._form')

<button class="btn btn-primary btn-block"type="submit">Update!</button>
</form>

@endsection