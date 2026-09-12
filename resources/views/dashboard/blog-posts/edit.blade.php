@extends('dashboard.layout')
@section('title', 'Edit post')
@section('content')
    <h1>Edit {{ $post->title }}</h1>
    @include('dashboard.blog-posts._form', ['post' => $post])
@endsection
