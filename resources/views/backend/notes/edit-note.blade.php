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
                    <h3 class="block-title font-w700">Nota Cantiere</h3>
                </div>
                <div class="block-content">

                    @if(!empty($enableSuperAdminEdit))
                    <form method="POST" action="{{ route('update_note', $note->id) }}">
                        @csrf

                        @if (isset($note->id)){{ method_field('PATCH') }}@endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif

                        <div class="form-group row pb-20">
                            <div class="col-12">
                                <label>Testo della nota</label><br />
                                @if(!empty($enableSuperAdminEdit))
                                    <textarea class="form-control" name="body" minlength="10" rows="4" required>{{ $note->body }}</textarea>
                                @else
                                {{ $note->body }}
                                @endcan
                            </div>
                        </div>

                        <div class="row pb-20">
                            @if(session()->has('allow_notes_img_upload') or !empty($enableSuperAdminEdit))
                            <div class="col-12">
                                <label for="search-modal-text">Se desideri, puoi aggiungere una o più immagini alla tua nota <small>(facoltativo)</small></label>
                                <div id="dropzonediv" class="dropzone sortable"></div>
                            </div>
                            @endif

                            @if(!session()->has('allow_notes_img_upload'))
                                <div class="col-12">
                                    <label for="search-modal-text">Immagini caricate per questa nota</label>
                                </div>
                                @foreach($note->media()->get() as $media)
                                <div class="col-3">
                                    <a class="img-link img-link-zoom-in img-thumb img-lightbox" href="{{ $media->getFullPath() }}">
                                        <img class="img-fluid" src="{{ $media->getFullPath('thumb') }}" alt="">
                                    </a>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        @if(auth()->user()->isAdmin())
                        <div class="row pb-20">
                            <div class="col-6">
                                <label>Dipendente</label>
                                <p>{{ $note->created_by }}</p>
                            </div>
                            <div class="col-6">
                                <label>Data di creazione</label>
                                <p>{{ $note->created_at }}</p>
                            </div>
                        </div>
                        @endif


                        <div class="block">
                        </div>

                        <div class="form-group row">

                            <div class="col-12 btn-right">

                                <a href="javascript:history.back(1)" class="btn btn-alt-secondary">
                                    <i class="fa fa-remove mr-5"></i> Chiudi
                                </a>

                                @if(!empty($enableSuperAdminEdit))
                                    <button type="submit" class="btn btn-success fes-btn-w">
                                        <i class="fa fa-save mr-5"></i> Salva
                                    </button>
                                @endif
                            </div>
                        </div>


                    @if(!empty($enableSuperAdminEdit))
                    </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')

    @if(session()->has('allow_notes_img_upload') or !empty($enableSuperAdminEdit))
    <script src="{{ asset('backend/js/plugins/dropzonejs/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function () {
            // richiedi posizione gps
            getLocation();

            // Update the media list when the upload has finished
            $('#dropzonediv').dropzone({
                maxFiles: 100,
                url: uploadMediaApi,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                params: {
                    'mediable_id': {{ $note->buildingSite->id }},
                    'mediable_type': 'App\\BuildingSite',
                    'note_id': {{ $note->id }}
                },
                success: function (response) {
                    console.log(response);
                }
            });

            // Enable magnific popup
            $('.img-link').magnificPopup({
                delegate: 'a',
                type:'image'
            });
        });

    </script>
    @endif

    <script>
        $(document).ready(function () {
            // Enable magnific popup
            $('.img-link').magnificPopup({
                type:'image'
            });
        });

    </script>
@endsection