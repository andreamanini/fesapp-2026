<!doctype html>
<html lang="it" class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>FES servizi login</title>

    <meta name="robots" content="noindex, nofollow">

    <link rel="shortcut icon" href="{{ asset('backend/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('backend/favicons/favicon-192x192fesy.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/favicons/apple-touch-icon-180x180fesy.png') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,400i,600,700">
    <link rel="stylesheet" id="css-main" href="{{ asset('backend/css/codebase.min.css') }}">
</head>
<body>

<div id="page-container" class="main-content-boxed">

    <!-- Main Container -->
    <main id="main-container">

        <!-- Page Content -->
        <div class="bg-image" style="background-image: url('{{ asset('backend/images/farinon-login.jpg') }}');">
            <div class="row mx-0 bg-black-op">
                <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
                    <div class="p-30 invisible" data-toggle="appear">
                        <p class="font-italic text-white-op">
                            Copyright &copy; <span class="js-year-copy">2021</span> Fes servizi s.r.l.</p>
                    </div>
                </div>
                <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-white invisible" data-toggle="appear" data-class="animated fadeInRight">
                    <div class="content content-full">
                        <!-- Header -->
                        <div class="px-30 py-10">
                            <img src="{{ asset('backend/images/fes-logo.svg') }}" width="250px" />
                            <h1 class="h3 font-w700 mt-30 mb-10">Gestionale FES servizi</h1>
                            <h2 class="h5 font-w400 text-muted mb-0">Effettua l'accesso</h2>
                        </div>
                        <!-- END Header -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="form-material floating">
                                        <input type="text" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" required>
                                        <label for="email">E-mail</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="form-material floating">
                                        <input type="password" class="form-control" id="password"
                                               name="password" required>
                                        <label for="password">Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                        <label class="custom-control-label" for="remember">Rimani connesso</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary">
                                    <i class="si si-login mr-10"></i> Accedi
                                </button>
                                <div class="mt-30">
                                    @if (Route::has('password.request'))
                                        <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="{{ route('password.request') }}">
                                            <i class="fa fa-warning mr-5"></i> Recupera password
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </form>
                        <!-- END Sign In Form -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END Page Content -->

    </main>
    <!-- END Main Container -->
</div>
<!-- END Page Container -->

<script src="{{ asset('backend/js/codebase.core.min.js') }}"></script>
<script src="{{ asset('backend/js/codebase.app.min.js') }}"></script>
</body>
</html>