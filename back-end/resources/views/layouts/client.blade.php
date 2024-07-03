<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>  @yield('title')</title>

        @yield('css')

    <link rel="stylesheet" href="{{asset('assets/clients/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/clients/css/bootstrap.min.css')}}">
</head>
<body>
    <header>
        <h1> HEADER</h1>
    </header>
    <main>
        <aside>
            @section('sidebar')
            @include('clients.blocks.sidebar')
            @show
        </aside>
        <div class="content">
            @yield('content')
        </div>
    </main>
    <footer>
        <a href="{{route('logout')}}">LOGOUT</a>
    </footer>
{{--    <script src="{{asset('assets/clients/js/bootstrap.min.js')}}"></script>--}}
    <script src="{{asset('assets/clients/js/custom.js')}}"></script>
    @yield('js')
</body>
</html>
