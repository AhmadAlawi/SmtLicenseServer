@extends('dashboard.layout')
@section('title', 'Edit plan')
@section('content')
    <h1>Edit {{ $plan->name }}</h1>
    @include('dashboard.plans._form', ['plan' => $plan])
@endsection
