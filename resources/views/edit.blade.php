@extends('layouts.base')
@section('title')
<title>{{ config('app.name', 'Laravel') }}</title>
@endsection
@section('content')
<h2 class="">Edit Container</h2>
<form action="/ct/update" method="post" class="uk-form-horizontal" id="update">
    @csrf
    <input type="text" name="name" value="{{ $data['Name'] }}" hidden="">
	<div class="uk-margin">
		<label class="uk-form-label" for="name">Name</label>
		<div class="uk-form-controls">
		    <input class="uk-input uk-form-width-large" id="name" type="text" placeholder="" value="{{ $data['Name'] }}" disabled="">
		</div>	
	</div>
	<div class="uk-margin">
		<label class="uk-form-label" for="hostname">Hostname</label>
		<div class="uk-form-controls">
		    <input class="uk-input uk-form-width-large" id="hostname" type="text" placeholder="" value="{{ $data['Hostname'] }}" name="hostname">
		</div>	
	</div>
    <div class="uk-margin">
    	<label class="uk-form-label" for="description">Description</label>

    	<div class="uk-form-controls">
        	<textarea id="description" class="uk-textarea uk-form-width-large" rows="2" placeholder="Textarea" name="description">{{ $data['Description'] }}</textarea>
    	</div>
    </div>	
    <div class="uk-margin">
        <div class="uk-form-controls"><label for="userpasswd"><input class="uk-checkbox uk-margin-small-right" id="userpasswd" name="password" type="checkbox">Сбросить пароль</label></div>
    </div>
    <div class="uk-margin">
    	<div class="uk-form-controls">
    		<a href="javascript:{}" onclick="document.getElementById('update').submit();" class="uk-button uk-button-primary uk-margin-small-right uk-margin-small-bottom">Сохранить</a> <a href="/ct/{{ $data['Name'] }}" class="uk-button uk-button-default uk-margin-small-bottom">Отмена</a>
    	</div>
    </div>
</form>
@endsection
