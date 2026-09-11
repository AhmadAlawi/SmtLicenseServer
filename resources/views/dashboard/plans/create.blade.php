@extends('dashboard.layout')
@section('title', 'New plan')
@section('content')
    <h1>New plan</h1>
    @include('dashboard.plans._form', ['plan' => null])
@endsection
