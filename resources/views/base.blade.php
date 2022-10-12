<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Discovery Visa System</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
  <div class="container">
	@if (Route::has('login'))
	<div class="top-right links">
		@auth
			<a href="{{ url('/home') }}">Home</a>
		@else
			<a href="{{ route('login') }}">Login</a>

			@if (Route::has('register'))
				<a href="{{ route('register') }}">Register</a>
			@endif
		@endauth
	</div>
	@endif
    @yield('main')
  </div>
  <script src="{{ asset('js/app.js') }}" type="text/js"></script>
</body>
</html>