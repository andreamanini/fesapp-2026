@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Utenti app / dipendenti</h3>
                </div>
                <div class="block-content">
                    <form method="POST"
                          @if (isset($employee->id))
                          action="{{ route('employees.update', $employee->id) }}"
                          @else
                          action="{{ route('employees.store') }}"
                          @endif>
                        @csrf

                        @if (isset($employee->id)){{ method_field('PATCH') }}@endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group row">
                            <div class="col-6">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ $employee->name ?? old('name') }}"
                                       placeholder="Inserisci il nome del dipendente.." required>
                            </div>
                            <div class="col-6">
                                <label for="surname">Cognome</label>
                                <input type="text" class="form-control" id="surname" name="surname"
                                       value="{{ $employee->surname ?? old('surname') }}"
                                       placeholder="Inserisci il cognome del dipendente.." required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="email">Email</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ $employee->email ?? old('email') }}"
                                           placeholder="Inserisci la email del dipendente.." required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Password.."
                                       @if(!isset($employee)){{ 'required' }}@endif>

                            </div>
                            <div class="col-6">
                                <label for="password_confirmation">Conferma Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation" placeholder="Conferma Password.."
                                       @if(!isset($employee)){{ 'required' }}@endif>
                            </div>
                            @isset($employee)
                            <div class="col-md-12">
                                <small>Compilando questi due campi, la password per questo utente verrà reimpostata</small>
                            </div>
                            @endisset
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="telephone">Telefono</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <input type="tel" class="form-control" id="telephone" name="telephone"
                                           value="{{ $employee->telephone ?? old('telephone') }}"
                                           placeholder="Numero di telefono del dipendente..">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-phone fa"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-6">
                                <label for="role">Ruolo di accesso all app</label>
                                <div class="input-group">
                                    <select type="text" class="form-control" id="role" name="role" required>
                                        <option value="">Seleziona ruolo di accesso all app</option>
                                        @foreach($userRoles as $key => $val)
                                            <option value="{{ $key }}" @if(isset($employee) and $key == $employee->role){{ 'selected' }}@endif>
                                                {{ $val }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="active">Attiva accesso all app</label>
                                <div class="input-group">
                                    <select type="text" class="form-control" id="active" name="active" required>
                                        <option value="1" @if(!isset($employee) or (isset($employee) and 1 == $employee->active)){{ 'selected' }}@endif>Si</option>
                                        <option value="0" @if(isset($employee) and 0 == $employee->active){{ 'selected' }}@endif>No</option>
                                    </select>
                                </div>
                                <small>Disattivanto l'accesso alla app questo utente non potrà più accedere.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12 btn-right">
                                <a href="{{ route('employees.index') }}" class="btn btn-alt-secondary">
                                    <i class="fa fa-remove mr-5"></i> Annulla
                                </a>
                                @if(auth()->user()->can('create', \App\User::class) or
                                    (isset($employee) and auth()->user()->can('update', $employee)))
                                <button type="submit" class="btn btn-success fes-btn-w">
                                    <i class="fa fa-save mr-5"></i> Salva
                                </button>
                                @endif
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection