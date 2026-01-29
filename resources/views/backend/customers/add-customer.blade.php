@extends('layouts.fes-app')

@section('content')
    <div class="row clienti">
        <div class="col-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Clienti</h3>
                </div>
                
                <div class="block-content">
                    <form method="POST"
                          @if (isset($customer->id))
                          action="{{ route('customers.update', $customer->id) }}"
                          @else
                          action="{{ route('customers.store') }}"
                          @endif>
                        @csrf

                        @if (isset($customer->id)){{ method_field('PATCH') }}@endif

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
                                <label for="company_name">Ragione sociale</label>
                                <input type="text" class="form-control" id="company_name"
                                       name="company_name"
                                       value="{{ $customer->company_name ?? old('company_name') }}"
                                       placeholder="Inserisci la ragione sociale.." required>
                            </div>
                            <div class="col-6">
                                <label for="manager">Nome referente</label>
                                <input type="text" class="form-control" id="manager" name="manager"
                                       value="{{ $customer->manager ?? old('manager') }}"
                                       placeholder="Inserisci nome referente..">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-6">
                                <label for="vatnumber">Partita IVA</label>
                                <input type="text" class="form-control" id="vatnumber"
                                       name="vatnumber"
                                       value="{{ $customer->vatnumber ?? old('vatnumber') }}"
                                       placeholder="Inserisci la partita iva..">
                            </div>
                            <div class="col-6">
                                <label for="taxcode">Codice Fiscale</label>
                                <input type="text" class="form-control" id="taxcode" name="taxcode"
                                       value="{{ $customer->taxcode ?? old('taxcode') }}"
                                       placeholder="Inserisci il codice fiscale" pattern="([a-zA-Z]{6}\d{2}[a-zA-Z]\d{2}[a-zA-Z]\d{3}[a-zA-Z])|(\d{11})">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-6">
                                <label for="sdi">Codice SDI</label>
                                <input type="text" class="form-control" id="sdi"
                                       name="sdi"
                                       value="{{ $customer->sdi ?? old('sdi') }}"
                                       placeholder="Inserisci il codice SDI..">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-12" for="email">Email</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email"
                                           name="email"
                                           value="{{ $customer->email ?? old('email') }}"
                                           placeholder="Inserisci l'email del cliente..">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                        <button type="button" id="display-emails" class="btn btn-success"
                                                @if(isset($customer->email2) or isset($customer->email3)){!! 'style="display:none"' !!}}@endif
                                        >+</button>
                                        <button type="button" id="hide-emails" class="btn btn-danger"
                                                @if(!isset($customer->email2) and !isset($customer->email3)){!! 'style="display:none"' !!}}@endif
                                        >-</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="extra-emails" @if(!isset($customer->email2) and !isset($customer->email3)){!! 'style="display:none"' !!}}@endif>
                            <div class="form-group row">
                                <label class="col-12" for="email2">Email 2</label>
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="email2" class="form-control" id="email2"
                                               name="email2"
                                               value="{{ $customer->email2 ?? old('email2') }}"
                                               placeholder="Inserisci l'email del cliente..">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-12" for="email3">Email 3</label>
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="email3" class="form-control" id="email3"
                                               name="email3"
                                               value="{{ $customer->email3 ?? old('email3') }}"
                                               placeholder="Inserisci l'email del cliente..">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br /><hr />
                        
                        <div class="form-group row">
                            <label class="col-12" for="telephone">Telefono</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <input type="tel" class="form-control" id="telephone"
                                           name="telephone"
                                           value="{{ $customer->telephone ?? old('telephone') }}"
                                           placeholder="Numero di telefono..">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-phone"></i>
                                        </span>

                                        <button type="button" id="display-phones" class="btn btn-success"
                                                @if(isset($customer->telephone2) or isset($customer->telephone3)){!! 'style="display:none"' !!}}@endif
                                        >+</button>
                                        <button type="button" id="hide-phones" class="btn btn-danger"
                                                @if(!isset($customer->telephone2) and !isset($customer->telephone3)){!! 'style="display:none"' !!}}@endif
                                        >-</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="extra-phones" @if(!isset($customer->telephone2) and !isset($customer->telephone3)){!! 'style="display:none"' !!}}@endif>
                            <div class="form-group row">
                                <label class="col-12" for="telephone2">Telefono 2</label>
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="tel" class="form-control" id="telephone2"
                                               name="telephone2"
                                               value="{{ $customer->telephone2 ?? old('telephone2') }}"
                                               placeholder="Numero di telefono..">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-12" for="telephone3">Telefono 3</label>
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="tel" class="form-control" id="telephone3"
                                               name="telephone3"
                                               value="{{ $customer->telephone3 ?? old('telephone3') }}"
                                               placeholder="Numero di telefono..">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br /><hr />
                        
                        <div class="form-group row">
                            <label class="col-12" for="address">Indirizzo</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="address"
                                           name="address"
                                           value="{{ $customer->address ?? old('address') }}"
                                           placeholder="Indirizzo completo">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-address-card"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-6">
                                <label for="address">Citt&agrave;</label>
                                <input type="text" class="form-control" id="city"
                                       name="city"
                                       value="{{ $customer->city ?? old('city') }}"
                                       placeholder="Citt&agrave;">
                            </div>

                            <div class="col-6">
                                <label for="address">CAP</label>
                                <input type="text" class="form-control" id="postcode"
                                       name="postcode"
                                       value="{{ $customer->postcode ?? old('postcode') }}"
                                       placeholder="CAP">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-12 btn-right">
                                <a href="{{ route('customers.index') }}" class="btn btn-alt-secondary">
                                    <i class="fa fa-remove mr-5"></i> Annulla
                                </a>
                                
                                @if(auth()->user()->can('create', \App\Customer::class) or
                                    (isset($customer) and auth()->user()->can('update', $customer)))
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

@section('footer')
    <script>
        $(document).ready(function() {
            // Display extra phones
            $('#display-phones').click(function() {
                $(this).hide();
                $('#extra-phones').show();
                $('#hide-phones').show();
            });

            // Hide extra phones
            $('#hide-phones').click(function() {
                $(this).hide();
                $('#extra-phones').hide();
                $('#display-phones').show();
                $('#telephone2').val('');
                $('#telephone3').val('');
            });

            // Display extra emails
            $('#display-emails').click(function() {
                $(this).hide();
                $('#extra-emails').show();
                $('#hide-emails').show();
            });

            // Hide extra emails
            $('#hide-emails').click(function() {
                $(this).hide();
                $('#extra-emails').hide();
                $('#display-emails').show();
                $('#email2').val('');
                $('#email3').val('');
            });
        });
    </script>
@endsection