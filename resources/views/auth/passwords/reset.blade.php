@extends('layouts.app')

@section('content')
    <!-- Page Content -->
    <div class="bg-body-dark">
        <div class="row mx-0 justify-content-center">
            <div class="hero-static col-lg-6 col-xl-4">
                <div class="content content-full overflow-hidden">
                    <!-- Header -->
                    <div class="py-30 text-center">
                        <br />
                        <h1 class="h4 font-w700 mt-30 mb-10">Reimposta la tua password</h1>
                        <h2 class="h5 font-w400 text-muted mb-0">Inserisci la tua e-mail e la tua nuova password.</h2>
                    </div>
                    <!-- END Header -->

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        @if ($errors->any())
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="block block-themed">
                            <div class="block-header fes-gr-colr">
                                <h3 class="block-title text-black">Recupero password</h3>
                            </div>

                            <div class="block-content">
                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="email" name="email"
                                               value="{{ $email ?? old('email') }}"
                                               required autocomplete="email" autofocus>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Nuova Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control"
                                               name="password" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Conferma Password</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control"
                                               name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-alt-success">
                                        Imposta nuova password
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>
                    <!-- END Reminder Form -->
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection