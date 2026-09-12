@extends('dashboard.layout')
@section('title', 'Select account')
@section('content')
    <h1>Select ad account &amp; Page</h1>
    <form method="POST" action="{{ route('dashboard.social.select-account') }}" style="max-width:480px">
        @csrf

        <label>Ad account</label>
        @foreach ($adAccounts as $account)
            <label style="font-weight:normal"><input type="radio" name="ad_account_id" value="{{ $account['id'] }}" {{ $loop->first ? 'checked' : '' }} required> {{ $account['name'] ?? $account['id'] }}</label>
        @endforeach

        @if (count($pages))
            <label>Page (used as the ad's identity)</label>
            @foreach ($pages as $page)
                <label style="font-weight:normal"><input type="radio" name="page_id" value="{{ $page['id'] }}" {{ $loop->first ? 'checked' : '' }}> {{ $page['name'] ?? $page['id'] }}</label>
            @endforeach
        @endif

        <button type="submit" class="btn" style="margin-top:16px">Connect</button>
    </form>
@endsection
