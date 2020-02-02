@extends('layouts.base')
@section('title')
<title>{{ config('app.name', 'Laravel') }}</title>
@endsection
@section('content')
<h2 class="">Rebuild Container</h2>
<form action="/ct/" method="post" class="uk-form" id="update">
    @csrf
    <input type="text" name="name" value="{{ $data['Name'] }}" hidden="">

    <div class="uk-child-width-1-3@m" uk-grid>
        <div>
            <h4 class="uk-heading-divider uk-flex uk-flex-middle"><img src="https://ovz.vcloud.net.ru/img/centos7.png" alt="" class="uk-margin-small-right">Centos</h4>
            <div class="uk-margin">
                <label><input class="uk-radio" type="radio" name="radio2" checked> Centos 7</label>
            </div>

            <div class="uk-margin">
                <label><input class="uk-radio" type="radio" name="radio2"> Centos 8</label>
            </div>        
        </div>
        <div>
            <h4 class="uk-heading-divider uk-flex uk-flex-middle"><img src="https://ovz.vcloud.net.ru/img/debian.png" alt="" class="uk-margin-small-right">Debian</h4>
            <div class="uk-margin">
                <label><input class="uk-radio" type="radio" name="radio2"> Debian 9</label>
            </div>  
            <div class="uk-margin">
                <label><input class="uk-radio" type="radio" name="radio2"> Debian 10</label>
            </div>  
        </div>
        <div>
            <h4 class="uk-heading-divider uk-flex uk-flex-middle"><img src="https://ovz.vcloud.net.ru/img/ubuntu.png" alt="" class="uk-margin-small-right">Ubuntu</h4>
            <div class="uk-margin">
                <label><input class="uk-radio" type="radio" name="radio2"> Ubuntu 18.04</label>
            </div>      
            <div class="uk-margin">
                <label><input class="uk-radio" type="radio" name="radio2" disabled=""> Ubuntu 20.04</label>
            </div>      
        </div>
    </div>

    @if($data['State'] == 'running')
    <div class="uk-margin">
        <a class="uk-alert-close"></a>
        <div class="uk-alert uk-alert-primary">Контейнер должен быть остановлен!</div>
    </div>
    <a href="/ct/{{ $data['Name'] }}" class="uk-button uk-button-default uk-margin-small-bottom">Отмена</a>
    @else
    <div class="uk-margin">
    	<div class="uk-form-controls">
    		<a href="javascript:{}" onclick="document.getElementById('update').submit();" class="uk-button uk-button-primary uk-margin-small-right uk-margin-small-bottom">Форматировать</a> <a href="/ct/{{ $data['Name'] }}" class="uk-button uk-button-default uk-margin-small-bottom">Отмена</a>
    	</div>
    </div>
    @endif
</form>
@endsection
