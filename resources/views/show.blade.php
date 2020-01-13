@extends('layouts.base')
@section('title')
<title>{{ config('app.name', 'Laravel') }}</title>
@endsection
@section('content')
<h2 class="">Container</h2>
<div id="data">   
    <div class="uk-height-medium uk-flex uk-flex-middle uk-flex-center">
        <span uk-spinner="ratio: 1.5"></span>
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

}, 4000);  
</script>   
@endsection
