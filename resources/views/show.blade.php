@extends('layouts.base')
@section('title')
<title>{{ config('app.name', 'Laravel') }}</title>
@endsection
@section('content')
<h2 class="">Container</h2>
@if (Session::has('pwgen'))
<div class="uk-alert uk-alert-primary" uk-alert>
    <a class="uk-alert-close" uk-close></a>
    Пароль root: {{ Session::get('pwgen') }}
</div>
@endif
<div id="data">   
    <div class="uk-height-medium uk-flex uk-flex-middle uk-flex-center">
        <span uk-spinner="ratio: 1.5"></span>
    </div>
</div>

<div id="agent" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical uk-width-large">
        <button class="uk-modal-close-default" type="button" uk-close></button>
		<h3 class="">Агент статистики</h3>
		<p>Инструкция по установке агента статистики.</p>
    </div>
</div>

<div id="restart" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical uk-width-medium uk-text-center">
        <button class="uk-modal-close-default" type="button" uk-close></button>
		<h3 class="">Подтвердить</h3>
        <a href="/ct/{{ $id }}/state/restart" class="uk-button uk-button-primary uk-margin-small-bottom">Перезагрузить</a>

    </div>
</div>

<div id="stop" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical uk-width-medium uk-text-center">
        <button class="uk-modal-close-default" type="button" uk-close></button>
		<h3 class="">Подтвердить</h3>
        <a href="/ct/{{ $id }}/state/stop" class="uk-button uk-button-primary uk-margin-small-bottom">Остановить</a>

    </div>
</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
function blockIndex( fn, delay ) {
    fn();
    setInterval( fn, delay );
}
blockIndex( function() {
axios.get('https://ovz.vcloud.net.ru/ct/{{ $id }}/show')
  .then(function (response) {
    document.getElementById("data").innerHTML  = response.data;
  })

}, 2500);  
</script>   
@endsection
