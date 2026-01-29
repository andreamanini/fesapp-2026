<!doctype html>
<html lang="it" class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Codebase - Bootstrap 4 Admin Template &amp; UI Framework</title>

    <meta name="robots" content="noindex, nofollow">

    <link rel="shortcut icon" href="{{ asset('backend/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('backend/favicons/favicon-192x192fesy.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/favicons/apple-touch-icon-180x180fesy.png') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,400i,600,700">
    <link rel="stylesheet" id="css-main" href="{{ asset('backend/css/codebase.min.css') }}">

    <style>
        .fes-gr-colr{
            background: rgb(255,215,64)!important;
            background: linear-gradient(270deg, rgba(255,215,64,1) 54%, rgba(249,181,0,1) 100%)!important;
        }
    </style>
</head>
<body>

<div id="page-container" class="main-content-boxed">

    <!-- Main Container -->
    <main id="main-container">

        @yield('content')

    </main>
    <!-- END Main Container -->
</div>
<!-- END Page Container -->

<script src="assets/js/codebase.core.min.js"></script>

<script src="assets/js/codebase.app.min.js"></script>

<script src="assets/js/pages/op_auth_reminder.min.js"></script>

</body>
</html>