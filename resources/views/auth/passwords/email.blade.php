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
                        <h1 class="h4 font-w700 mt-30 mb-10">Recupero password</h1>
                        <h2 class="h5 font-w400 text-muted mb-0">Inserisci il tuo indirizzo e-mail per recuperare la password.</h2>
                    </div>
                    <!-- END Header -->

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="block block-themed">
                            <div class="block-header fes-gr-colr">
                                <h3 class="block-title text-black">Recupero password</h3>
                            </div>

                            <div class="block-content">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}"
                                               required autocomplete="email" autofocus>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-alt-success">
                                        <i class="fa fa-asterisk mr-10"></i> Recupera password
                                    </button>
                                </div>
                            </div>

                            <div class="block-content bg-body-light">
                                <div class="form-group text-center">
                                    <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="{{ route('login') }}">
                                        <i class="fa fa-user text-muted mr-5"></i> Accedi
                                    </a>
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
