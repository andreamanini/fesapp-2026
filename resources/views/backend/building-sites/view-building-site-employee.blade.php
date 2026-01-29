@extends('layouts.fes-app')

@section('header')
    <link rel="stylesheet" href="{{ asset('backend/js/plugins/select2/css/select2.min.css') }}"/>
    <style>
    </style>
@endsection

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Dettagli Cantiere</h3>
                </div>
                <div class="block-content">

                    <div class="form-group row pb-20">
                        <div class="col-6">
                            <label>Nome cantiere</label><br />
                            {{ $buildingSite->site_name }}
                        </div>


                        <div class="col-6">
                            <label>Nome Cliente</label><br />
                            {{ $buildingSite->customer->company_name }}
                            @if(null !== $buildingSite->customer->manager)
                                - (Referente: {{ $buildingSite->customer->manager }})
                            @endif
                        </div>
                    </div>

                    <div class="form-group row pb-20">
                        <label class="col-12" for="manager_id">Responsabile di cantiere</label>
                        <div class="col-12">
                            @if(null !== $buildingSite->manager)
                                {{ $buildingSite->manager->name }} {{ $buildingSite->manager->surname }}
                            @endif
                        </div>
                    </div>

                    {{--<div class="form-group row">--}}
                        {{--<label class="col-12" for="site_type">Tipologia Cantiere</label>--}}
                        {{--<div class="col-12">--}}
                            {{--<div class="input-group">--}}
                                {{--<select class="js-select2 form-control" id="site_type" name="site_type[]"--}}
                                        {{--style="width: 100%;" data-placeholder="Seleziona una o più tipologie" multiple>--}}
                                    {{--<option></option> --}}{{-- mantenere questo tag serve per il funzionamento del select2 --}}
                                    {{--@foreach($buildingSyteTypes as $btype)--}}
                                        {{--<option value="{{ strtolower($btype) }}"--}}
                                        {{--@if(isset($buildingSite) and in_array(strtolower($btype), $buildingSite->site_type)){{ 'selected' }}@endif>{{ $btype }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <label>Indirizzo del cantiere</label><br />
                            @if($buildingSite->address and isset($buildingSite->address->route))
                                {{ $buildingSite->address->route }}{{ $buildingSite->address->street_number }}, {{ $buildingSite->address->locality }} -
                                <a href="https://www.google.com/maps/dir//{{ $buildingSite->address->lat }},{{ $buildingSite->address->lng }}/{{ '@'.$buildingSite->address->lat }},{{ $buildingSite->address->lng }},13z/" target="_blank"><i class="fa fa-map-marker"></i> Apri google maps</a>
                                {{-- <a href="ms-drive-to:?destination.latitude={{ $buildingSite->address->lat }}&destination.longitude={{ $buildingSite->address->lng }}" target="_blank">Avvia navigazione</a> --}}
                            @else
                                Indirizzo non specificato.
                            @endif

                        </div>
                    </div>

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <label>Note importanti</label><br />

                            @if($buildingSite->buildingSiteNotes()->count() > 0)

                            <div id="accordion" class="bs-notes-accordion">
                                @foreach($buildingSite->buildingSiteNotes()->get() as $bsNote)
                                <div class="card">
                                    <div class="card-header bs-notes-card-header" id="heading{{ $loop->index }}">
                                        <h5 class="mb-0 accordion-card-title"
                                            data-toggle="collapse" data-target="#collapse{{ $loop->index }}" aria-expanded="true"
                                            aria-controls="collapse{{ $loop->index }}"
                                        >
                                                @if(!empty($bsNote->note_title)){{ $bsNote->note_title }}@else{{ 'Nota senza titolo' }}@endif
                                                @isset($bsNote->note_date){!! '<span style="margin-left:25px">' !!}{{ $bsNote->note_date }}{!! '</span>' !!}@endisset
                                        </h5>
                                        <i class="fa fa-caret-down"></i>
                                    </div>

                                    <div id="collapse{{ $loop->index }}" class="collapse" aria-labelledby="heading{{ $loop->index }}" data-parent="#accordion">
                                        <div class="card-body">
                                            {!! $bsNote->note_body !!}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                                Nessuna nota relativa a questo cantiere.
                            @endif
                        </div>
                    </div>

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <label>Materiali necessari</label>

                            @if(isset($materials) and isset($materials->materials) and isset($materials->qty))
                                @for($m=0; $m<count($materials->materials); $m++)
                                    @if(isset($materials->materials[$m]) and isset($materials->qty[$m]))
                                        <div class="col-12">
                                            <label class="css-control-lg css-control-success css-checkbox">
                                                <input type="checkbox" class="css-control-input" />
                                                <span class="css-control-indicator"></span> {{ $materials->materials[$m] }} - (quantità: {{ $materials->qty[$m] }})
                                            </label>
                                        </div>
                                    @endif
                                @endfor
                            @endif
                        </div>
                    </div>

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <label class="d-block">Macchinari o attrezzature necessari</label>

                            @php
                                $selectedMachines = $buildingSite->machineries();
                            @endphp
                            @foreach($selectedMachines->get() as $machine)

                                <div class="col-12">
                                    <label class="css-control-lg css-control-success css-checkbox">
                                        <input type="checkbox" class="css-control-input" />
                                        <span class="css-control-indicator"></span>
                                        @if(null !== $machine->mainImage())
                                            <img src="{{ $machine->mainImage()->getFullPath('thumb') }}" alt="{{ $machine->machine_name }}" width="100px">
                                        @else
                                            <img src="{{ asset('backend/images/machinery-no-image.jpg') }}" alt="" width="100px">
                                        @endif
                                        {{ $machine->machine_name }} @isset($machine->machine_number)( Numero: {{ $machine->machine_number }} )@endisset
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <label>Dettagli visivi del cantiere</label><br />
                            <small>Clicca su una delle immagini sottostanti per vedere eventuali note inserite dal titolare</small>

                            <div class="row pt-10">
                                @foreach($bsMedia as $media)
                                    <div class="col-3 pb-20">
                                        <a href="{{ route('tag_image', $media->id) }}">
                                            <img src="{{ asset($media->getFullPath('thumb')) }}"
                                                 style="max-width: 150px"
                                                 alt="clicca sull'immagine per visualizzarne le note" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group row pb-20">
                        <div class="col-12">
                            <label>Documenti relativi al cantiere</label>

                            <ul>
                                @foreach($buildingSite->media('file')->get() as $file)
                                    <li><a href="{{ route('download_media_file', $file->id) }}">{{ $file->media_name }} ({{ $file->extension }})</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="block">
                    </div>
                    @if(!$mediaToday)
                    <div class="alert alert-danger">
                        Attenzione! per poter compilare il rapportino devi caricare almeno 5 fotografie relative alla giornata di oggi.
                    </div>
                    @endif
                    <div class="form-group row">
                        <div class="col-8 btn-left">
                            <!-- fare controllo sulle 5 foto doge fes -->
                            <!-- <?php echo"Numero foto allegate:".$mediaTodayCount; ?> -->
                            <?php if(!$mediaToday): ?>
                            <a onclick='alert("<?php echo "Non hai caricato abbastanza foto su questo cantiere oggi. Per compilare il rapportino devi inserirne 5, te ne hai caricate ${mediaTodayCount}" ?>")' class="btn btn-alt-primary">
                                <i class="fa fa-archive mr-5"></i> Compila rapportino
                            </a>                            
                            <?php else: ?>
                            <a href="{{ route('reports.create', $buildingSite->id) }}" class="btn btn-alt-primary">
                                <i class="fa fa-archive mr-5"></i> Compila rapportino
                            </a>
                            <?php endif ?>
                            <a href="{{ route('foglio_fine_cantiere', $buildingSite->id) }}" class="btn btn-alt-danger">
                                <i class="fa fa-archive mr-5"></i> Compila foglio fine cantiere
                            </a>

                            <a href="{{ route('bs_upload_media_fine_cantiere', $buildingSite->id) }}" class="btn btn-alt-info">
                                <i class="fa fa-camera mr-5"></i> Carica fotografie
                            </a>
                        </div>

                        <div class="col-4 btn-right">
                            <button class="btn btn-info" data-toggle="modal" data-target="#notes-modal">Aggiungi note</button>

                            <a href="{{ route('building-sites.index') }}" class="btn btn-alt-secondary mt-5">
                                <i class="fa fa-remove mr-5"></i> Chiudi
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="notes-modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('store_note') }}">
                    @csrf

                    <input type="hidden" id="location_lat" name="location_lat" value="" />
                    <input type="hidden" id="location_lng" name="location_lng" value="" />
                    <input type="hidden" id="building_site_id" name="building_site_id" value="{{ $buildingSite->id }}" />

                    <div class="modal-header">
                        <h5 class="modal-title">Inserisci delle note cantiere</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Inserisci una nota per questo cantiere, le note saranno visibili ai titolari ed all'amministrazione</p>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="search-modal-text">* Nota (minimo 10 caratteri)</label>
                                <textarea name="body" id="body" class="form-control" rows="8" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Salva nota</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('backend/js/plugins/select2/js/select2.min.js') }}"></script>
    <script>jQuery(function(){ Codebase.helpers(['select2']); });</script>
    <script>
        $(document).ready(function () {
            // richiedi posizione gps
            getLocation();

        });
    </script>
@endsection