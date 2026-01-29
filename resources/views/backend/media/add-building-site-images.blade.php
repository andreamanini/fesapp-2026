@extends('layouts.fes-app')

@section('header')
    <link rel="stylesheet" href="{{ asset('backend/js/plugins/dropzonejs/dist/dropzone.css') }}"/>
@endsection

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Carica immagini</h3>
                </div>
                <div class="block-content">

                    <input type="hidden" id="location_lat" name="lat" value="{{ old('lat') }}" />
                    <input type="hidden" id="location_lng" name="lng" value="{{ old('lng') }}" />

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <p>Cantiere: {{ $buildingSite->site_name }}</p>
                            <p>Trascina o carica immagini nell'apposito spazio, le immagini selezionate verranno catalogate per data,
                            ora e posizione geografica all'interno del sistema.<br />
                                Per assicurarsi che la posizione geografica venga salvata correttamente, bisogna consentire la rilevazione
                                da parte dell'applicazione.<br /><br />
                                Si consiglia inoltre di caricare le immagini nei pressi del cantiere in modo da avere una geolocalizzazione relativa
                                all'area geografica di lavoro.
                            </p>
                            <div id="dropzonediv" class="dropzone sortable">
                            </div>

                            <small>Le immagini inserite verranno automaticamente caricate ed assegnate al cantiere.</small>
                        </div>

                        <div class="col-12 pt-15" id="geo-location-error" style="display:none">
                            <div class="alert alert-danger">
                                Non è stato possibile rilevare la posizione corrente, assicurarsi di aver abilitato i
                                permessi di geolocalizzazione per questa app e <strong>ricaricare la pagina</strong>.
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-6">
                            <a href="{{ route('building-sites.show', $buildingSite->id) }}" class="btn btn-alt-secondary">
                                <i class="fa fa-remove mr-5"></i> Chiudi
                            </a>
                        </div>

                        <div class="col-6">
                            <a href="{{ route('bs_show_media_files', $buildingSite->id) }}" class="btn btn-info pull-right">
                                <i class="fa fa-eye mr-5"></i> Visualizza immagini caricate
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('backend/js/plugins/dropzonejs/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;

        // richiedi posizione gps
        getLocation();

        $(document).ready(function() {

            if (!$('#location_lat').val() || !$('#location_lng').val()) {
                $('#geo-location-error').show();
            }

            $('#dropzonediv').dropzone({
                maxFiles: 50,
                maxFilesize: 8,
                url: uploadMediaApi,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                params: {
                    'mediable_id': {{ $buildingSite->id }},
                    'mediable_type': 'App\\BuildingSite',
                    'lat': $('#location_lat').val(),
                    'lng': $('#location_lng').val(),
                    'job_proof': 1
                },
                success: function(response) {
                },
            });

        });
    </script>
@endsection