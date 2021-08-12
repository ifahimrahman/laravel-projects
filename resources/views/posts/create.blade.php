@extends('layout')

@section('content')

<form method="POST" action="{{route('posts.store')}}" enctype="multipart/form-data">
@csrf

@include('posts._form')

<button class="btn btn-primary btn-block" type="submit">Create!</button>
</form>

@endsection