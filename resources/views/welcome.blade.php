@extends('layouts.base')
<title>{{ config('app.name', 'Laravel') }}</title>
@section('content')
<div class="uk-flex uk-flex-center uk-flex-middle" style="min-height: calc(100vh - 161px);">
    <div class="">
        <h1 class="uk-heading-medium uk-text-muted uk-text-uppercase">{{ config('app.name', 'Laravel') }}</h1>
        @if (Route::has('login'))
        <ul class="uk-subnav uk-flex-center">
                @auth
                    <li><a href="{{ url('/home') }}">Home</a></li>
                @else
                    <li><a href="{{ route('login') }}">Login</a></li>
                    @if (Route::has('register'))
                    <li><a href="{{ route('register') }}">Register</a></li>
                    @endif
                @endauth
        </ul>
        @endif
    </div>
</div>
@endsection
