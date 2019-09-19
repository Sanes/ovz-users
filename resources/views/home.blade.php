@extends('layouts.base')
@section('title')
<title>{{ config('app.name', 'Laravel') }}</title>
@endsection
@section('content')
@if (session('status'))
    <div class="uk-alert uk-alert-primary" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        {{ session('status') }}
    </div>
@endif
<div class="uk-flex uk-flex-center uk-flex-middle" style="min-height: calc(100vh - 161px);">
    <div class="">
        <h1 class="uk-text-muted">Welcome {{ Auth::user()->name }}!</h1>
        <form action="{{ route('logout') }}" method="POST" class="uk-flex uk-flex-center">
            @csrf
            <button type="submit" class="uk-button uk-button-primary">Logout</button>
        </form>
    </div>
</div>
@endsection
