@extends('dashboard.layout')
@section('title', 'New post')
@section('content')
    <h1>New post</h1>
    @include('dashboard.blog-posts._form', ['post' => null])
@endsection
