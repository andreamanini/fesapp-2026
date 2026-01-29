@extends('layouts.fes-app')

@section('header')
    <link rel="stylesheet" href="{{ asset('backend/js/plugins/dropzonejs/dist/dropzone.css') }}"/>
    <link href="{{ asset('node_modules/lightbox2/dist/css/lightbox.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Carica immagini di fine Cantiere</h3>
                </div>
                <div class="block-content">

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <p>Cantiere: {{ $buildingSite->site_name }}</p>
                        </div>
                    </div>

                    <div class="form-group row pb-20">
                        @foreach($media->items() as $m)
                            @php
                                $coord = json_decode($m->coordinates);
                            @endphp
                        <div class="col-3">
                            <a class="img-link img-link-zoom-in img-thumb img-lightbox" data-lightbox="gallery" href="{{ asset("media/{$m->directory}/{$m->media_name}.{$m->extension}") }}">
                                <img class="img-fluid" src="{{ asset("media/{$m->directory}/thumb_{$m->media_name}.{$m->extension}") }}" alt="">
                            </a><br />
                            <small>
                                {{ $m->created_by }} il {{ $m->created_at->format('d/m/Y H:i') }}<br />
                                @if(isset($coord->lat) and isset($coord->lng))
                                <a href="https://www.google.com/maps/dir//{{ $coord->lat }},{{ $coord->lng }}/{{ '@'.$coord->lat }},{{ $coord->lng }},13z/" target="_blank">Geolocalizzazione</a>
                                @endif
                            </small>
                        </div>
                        @endforeach
                    </div>

                    <div class="form-group row">

                        <div class="col-2">
                            <a href="{{ route('building-sites.show', $buildingSite->id) }}" class="btn btn-alt-secondary">
                                <i class="fa fa-remove mr-5"></i> Chiudi
                            </a>
                        </div>
                        
                        <div class="col-10 pull-right">
                            <nav aria-label="Page navigation" class=" pull-right">
                                <ul class="pagination">
                                    {{ $media->links() }}
                                </ul>
                            </nav>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('node_modules/lightbox2/dist/js/lightbox.js') }}"></script>
    <script>
        $(document).ready(function() {
        //    $('.img-link').magnificPopup({type:'image'});
            lightbox.option({
              'resizeDuration': 500,
              'wrapAround': true,
              'albumLabel': "Immagine %1 di %2"
            })
        });
    </script>
@endsection