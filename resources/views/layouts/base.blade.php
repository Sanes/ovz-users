<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">	
	@yield('title')
	<link rel="stylesheet" href="/css/uikit.min.css" />

	<script src="/js/uikit.min.js"></script>
	<script src="/js/uikit-icons.min.js"></script>
<style>
	.uk-navbar-item, .uk-navbar-nav > li > a {padding: 0 10px;}
	.uk-icon-button {background: #e7e7e7;}
	.uk-icon-button:hover {background: #1e87f0; color: #fff;}
	tr:nth-child(even)  {background: #f8f8f8;}
	thead  {background: #f5f5f5;}
</style>	
</head>
<body>
	<div class="uk-background-primary uk-light uk-margin-bottom">
		<div class="uk-container">
	        <nav class="uk-navbar-container uk-navbar-transparent uk-margin" uk-navbar>
	        	<div class="uk-navbar-left">
	        		<div class="uk-navbar-item uk-padding-remove-horizontal">
		        		<a href="/" class="uk-logo uk-text-uppercase" style="color: #fff;"><span uk-icon="icon: server; ratio: 1.7" class="uk-margin-small-right"></span>{{ config('app.name', 'Laravel') }}</a>
	        		</div>
	        	</div>
	        	<div class="uk-navbar-right">
	        	@guest
	        		<ul class="uk-navbar-nav">
	        			<li><a href="/login"><span uk-icon="icon: user;" class="uk-margin-small-right"></span>Login</a></li>
	        		</ul>
	        	@endguest
	        	@auth
	        		<ul class="uk-navbar-nav">
	        			<li class=""><a href="#"><span uk-icon="icon: cart;" class="uk-margin-small-right"></span>Billing</a></li>
	        			@if(Request::is('ct') || Request::is('ct/*'))
	        			<li class="uk-active"><a href="/ct"><span uk-icon="icon: grid;" class="uk-margin-small-right"></span>Containers</a></li>
	        			@else
	        			<li><a href="/ct"><span uk-icon="icon: grid;" class="uk-margin-small-right"></span>Containers</a></li>
						@endif 
	        			<li>
            				<a href="javascript:{}" onclick="document.getElementById('logout').submit();"><span uk-icon="icon: sign-out;" class="uk-margin-small-right"></span>Logout</a>	        				
	        			</li>
	        		</ul>
			        <form action="{{ route('logout') }}" method="POST" class="uk-flex uk-flex-center" id="logout">
			            @csrf
			        </form>	        		
	        	@endauth
	        	</div>
	        </nav>				
		</div>
	</div>
	<div class="uk-container" style="min-height: calc(100vh - 161px);">
		@yield('content')
	</div>
	<section class="uk-section uk-section-xsmall uk-section-muted uk-text-center uk-text-small uk-text-uppercase" style="background: #e7e7e7;">
		&copy; @php echo date('Y'); @endphp
	</section>
</body>
</html>