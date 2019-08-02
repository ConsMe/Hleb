<!DOCTYPE html>
<html style="font-family: 'Open Sans', sans-serif;font-size: 16px;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>ХБИ</title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" href="assets/css/Navigation-Clean.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
</head>

<body style="font-family: 'Open Sans', sans-serif;padding-bottom:100px;">
    <div>
        <nav class="navbar navbar-light navbar-expand sticky-top navigation-clean">
            <div class="container"><a class="navbar-brand h2" href="#">ХБИ</a><button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse"
                    id="navcol-1">
                    <ul class="nav navbar-nav ml-auto">
                        <li class="nav-item" role="presentation"><a class="nav-link{{ Route::currentRouteName() == 'prices' ? ' active' : '' }}" href="/prices">Цены</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link{{ Route::currentRouteName() == 'products' ? ' active' : '' }}" href="/products">Изделия</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" href="#" id="logoutButton">Выйти</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    @yield('body')
    <!--@csrf-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript" src="js/csrf.js"></script>
    <script>
        $('#logoutButton').click(() => {
            let f = document.createElement("form")
            f.setAttribute('method',"post")
            f.setAttribute('action',"logout")
            let c = document.createElement('input')
            c.setAttribute('type', "hidden")
            c.setAttribute('name', "_token")
            c.setAttribute('value', $('meta[name="csrf-token"]').attr('content'))
            $(f).append(c)
            $('body').append(f)
            f.submit()
        })
    </script>
    @yield('scripts')
</body>

</html>