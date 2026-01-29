<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi note ad immagine</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
          integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('backend/css/image-tagging.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<main class="container">
    <input type="hidden" id="pos-x" value="" />
    <input type="hidden" id="pos-y" value="" />

    <div class="img-cont">
        <img  class="img-fluid" id="clickable-img" src="{{ $media->getFullPath() }}" alt="">

        @foreach($media->notes as $note)
            <span class="pin-info ttr-50" id="pin-{{ str_replace('.', '', $note->posx) }}{{ str_replace('.', '', $note->posy) }}"
                  style="left:{{ $note->posx }}%; top: {{ $note->posy }}%;"
                  data-pos-x="{{ $note->posx }}" data-pos-y="{{ $note->posy }}"
                  data-toggle="tooltip" title=""
                  data-original-title="{{ $note->title }}"></span>
        @endforeach
    </div>

    <p>Clicca su un punto dell'immagine per aggiungere una nota o clicca su una nota per modificarla o rimuoverla.</p><br />


    <div class="row">
        <div class="col-md-6">

            <a href="javascript:history.back(1);" class="btn btn-secondary">Torna indietro</a>

        </div>
        <div class="col-md-6">
            <button type="button" id="save-pin-status" class="btn btn-primary">Salva modifiche</button>
        </div>
    </div>

    <div class="row" style="padding-top:20px">
        <div class="col-md-12">
            <span id="success-msg" class="alert alert-success" style="display: none">Il salvataggio dei punti di interesse è stato completato correttamente.</span>
            <span id="error-msg" class="alert alert-danger" style="display: none">Si &egrave; verificato un errore durante il salvataggio dei punti di interesse.</span>
        </div>
    </div>

</main>

<!-- Modal for create pins-->
<div class="modal fade" id="img_tagging_modal" tabindex="-1" aria-labelledby="img_tagging_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="img_tagging_modalLabel">Inserisci commento alla foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <textarea class="form-control" id="pin-comment"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="remove-pin">Chiudi</button>
                <button id="save-pin" type="button" class="btn btn-primary">Salva modifiche</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for edit pins-->
<div class="modal fade" id="img_pin_modal" tabindex="-1" aria-labelledby="img_ping_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="img_pin_modalLabel">Modifica/Rimuovi commento alla foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <textarea class="form-control" id="pin-content"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button id="delete-pin" type="button" class="btn btn-danger" >Rimuovi tag</button>
                <button id="save-changes" type="button" class="btn btn-primary">Salva modifiche</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backend/js/jquery-3.5.1.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script>
    function toolTip() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    $(document).ready(function() {
        // Run the tooltip function when the page opens
        toolTip();
    });
</script>
@if(auth()->user()->isAdmin())
    {{-- only allow image tagging to admins --}}
<script>
    const mediaTaggingUrl = '{{ route('store_image_tags') }}';
    const mediaId = '{{ $media->id }}';
</script>
<script src="{{ asset('backend/js/image-tagging.js') }}"></script>
@endif
</body>
</html>