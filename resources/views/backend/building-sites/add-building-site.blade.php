@extends('layouts.fes-app')

@section('header')
    <link rel="stylesheet" href="{{ asset('backend/js/plugins/dropzonejs/dist/dropzone.css') }}"/>
    <link rel="stylesheet" href="{{ asset('backend/js/plugins/select2/css/select2.min.css') }}"/>
@endsection

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Aggiungi Cantiere</h3>
                </div>
                <div class="block-content">
                    <form method="POST" enctype="multipart/form-data"
                          @if (isset($buildingSite->id))
                          action="{{ route('building-sites.update', $buildingSite->id) }}"
                          @else
                          action="{{ route('building-sites.store') }}"
                          @endif>
                        @csrf

                        @if (isset($buildingSite->id)){{ method_field('PATCH') }}@endif

                        <input type="hidden" id="lat" name="lat"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->lat }}@else{{ old('lat') }}@endif" />

                        <input type="hidden" id="lng" name="lng"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->lng }}@else{{ old('lng') }}@endif" />

                        <input type="hidden" id="street_number" name="street_number"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->street_number }}@else{{ old('street_number') }}@endif" />

                        <input type="hidden" id="route" name="route"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->route }}@else{{ old('route') }}@endif" />

                        <input type="hidden" id="locality" name="locality"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->locality }}@else{{ old('locality') }}@endif" />

                        <input type="hidden" id="administrative_area_level_1" name="administrative_area_level_1"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->administrative_area_level_1 }}@else{{ old('administrative_area_level_1') }}@endif" />

                        <input type="hidden" id="country" name="country"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->country }}@else{{ old('country') }}@endif" />

                        <input type="hidden" id="postal_code" name="postal_code"
                               value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->postal_code }}@else{{ old('postal_code') }}@endif" />

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
                            <label class="col-12" for="site_name">Nome cantiere *</label>
                            <div class="col-12">
                                <input type="text" class="form-control" id="site_name" name="site_name"
                                       placeholder="Dai un nome indicativo per riconoscere questo cantiere"
                                       value="@isset($buildingSite){{ $buildingSite->site_name }}@else{{ old('site_name') }}@endisset"
                                       required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-12" for="customer_id">Assegna Cliente *</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <select class="js-select2 form-control" id="customer_id" name="customer_id"
                                            style="width: 100%;" data-placeholder="Assegna Cliente">
                                        <option value="">Assegna Cliente</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                            @if((isset($buildingSite) and $buildingSite->customer_id == $customer->id) or $customer->id == old('customer_id')){{ 'selected' }}@endif>
                                                {{ $customer->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-12" for="manager_id">Assegna responsabile di cantiere</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <select type="text" class="form-control" id="manager_id" name="manager_id" @isset($buildingSite){{ 'required' }}@endisset>
                                        <option value="">Assegna responsabile di cantiere</option>
                                        <!-- in questo elenco bisogna aggiungere Algisi Massimo e Sarzi Stefano -->
                                        <?php 
                                        use App\User;
                                        $user = new User();
                                        $employees_plus = $user->getEmployeeListPlus(false, true); 
                                        //nuova lista solo x "Assegna responsabile di cantiere" in /building-sites/create 
                                        ?>
                                        @foreach($employees_plus as $employee)
                                        <option value="{{ $employee->id }}"
                                        @if((isset($buildingSite) and $buildingSite->manager_id == $employee->id) or $employee->id == old('manager_id')){{ 'selected' }}@endif
                                        >{{ $employee->name }} {{ $employee->surname }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small>Assegnando un dipendente come responsabile, verr&agrave; inviata una notifica via e-mail alla persona selezionata.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-12" for="site_type">Assegna dipendenti al cantiere</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <select class="js-select2 form-control" id="employee_visibility" name="employee_visibility[]"
                                            style="width: 100%;" data-placeholder="Seleziona uno o più dipendenti" multiple>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                            @if((isset($buildingSite) and in_array($employee->id, $assignedEmployees)) or
                                                (null !== old('employee_visibility') and in_array($employee->id, old('employee_visibility')))){{ 'selected' }}@endif
                                            >{{ $employee->name }} {{ $employee->surname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="pb-5 pull-right">
                                <button type='button' class="btn btn-primary" name='check' onClick='selectAll()' >Inserisci Tutti</button>
                                </div>
                                <small>Assegnando uno o più dipendenti a questo cantiere permetterai loro di vederne le specifiche.
                                    Gli utenti selezionati riceveranno una notifica via e-mail.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-12" for="site_type">Tipologia Cantiere</label>
                            <div class="col-12">
                                <div class="input-group">
                                    <select class="js-select2 form-control" id="site_type" name="site_type[]"
                                            style="width: 100%;" data-placeholder="Seleziona una o più tipologie" multiple>
                                        <option></option> {{-- mantenere questo tag serve per il funzionamento del select2 --}}
                                        @foreach($buildingSyteTypes as $btype)
                                            <option value="{{ strtolower($btype) }}"
                                            @if((isset($buildingSite) and in_array(strtolower($btype), $buildingSite->site_type)) or
                                                (null !== old('site_type') and in_array(strtolower($btype), old('site_type')))){{ 'selected' }}@endif>{{ $btype }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Indirizzo del cantiere</label>
                            <input type="text" class="form-control" id="autocomplete" name="address"
                                   value="@if(isset($buildingSite) and $buildingSite->address){{ $buildingSite->address->autocomplete }}@else{{ old('address') }}@endif"
                                   placeholder="Inserisci l'indirizzo completo del cantiere" />
                        </div>

                        <div id="bs-notes">
                            @if(isset($buildingSite))
                                @if($buildingSite->buildingSiteNotes()->count() > 0)
                                    @foreach($buildingSite->buildingSiteNotes()->get() as $bsNote)
                                        @component('backend.components.bs-note', [
                                            'noteTitle' => $bsNote->note_title,
                                            'noteDate' => $bsNote->note_date,
                                            'noteBody' => $bsNote->note_body,
                                            'addButton' => (1 == $loop->iteration),
                                            'id' => $loop->index
                                        ])@endcomponent
                                    @endforeach
                                @else
                                    @component('backend.components.bs-note', [
                                        'addButton' => true,
                                        'id' => 0
                                    ])@endcomponent
                                @endif
                            @else
                                @component('backend.components.bs-note', [
                                    'addButton' => true,
                                    'id' => 0
                                ])@endcomponent
                            @endif
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label for="customer_notes">Descrizione lavoro per cliente</label>
                                <textarea class="form-control" id="customer_notes" name="customer_notes" rows="9">@isset($buildingSite){{ $buildingSite->customer_notes }}@else{{ old('customer_notes') }}@endisset</textarea>
                                <small>Questa dicitura comparirà automaticamente nella finestra del rapportino cliente all'interno del campo "descrizione lavoro"</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label>Dettagli per fatturazione</label>
                            </div>

                            <div class="col-4">
                                <label for="quote_number">Nr. offerta FES</label>
                                <input type="text" class="form-control" id="quote_number" name="quote_number"
                                       value="@isset($buildingSite){{ $buildingSite->quote_number }}@else{{ old('quote_number') }}@endisset" />
                            </div>

                            <div class="col-3">
                                <label for="quote_date">Offerta del</label>
                                <input type="text" class="form-control" id="quote_date" name="quote_date" pattern="\d{1,2}/\d{1,2}/\d{4}"
                                       placeholder="Clicca per inserire una data"
                                       value="@isset($buildingSite){{ $buildingSite->quote_date }}@else{{ old('quote_date') }}@endisset" />
                            </div>

                            <div class="col-4">
                                <label for="order_number">Rif. numero ordine</label>
                                <input type="text" class="form-control" id="order_number" name="order_number"
                                       value="@isset($buildingSite){{ $buildingSite->order_number }}@else{{ old('order_number') }}@endisset" />
                            </div>

                            <div class="col-12">
                                <small>Queste diciture compariranno automaticamente all'interno del SAL generato per questo cliente.</small>
                            </div>
                        </div>


                        @isset($buildingSite)
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Carica Documenti relativi al cantiere</label>

                                <div id="dropzonedocs" class="dropzone">
                                    <div class="dz-message" data-dz-message><span>Trascina qui i documenti relativi al cantiere</span></div>
                                </div>
                                {{--<div class="custom-file">--}}
                                    {{--<input type="file" class="custom-file-input"--}}
                                           {{--id="document_upload" name="document_upload"--}}
                                           {{--data-toggle="custom-file-input" multiple="">--}}
                                    {{--<label class="custom-file-label" for="document_upload">Seleziona documento</label>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                        @endisset

                        <div class="form-group row mb-30">
                            <div class="col-12 working-materials">
                                <label>Aggiungi materiali</label>
                                <div class="input-group">
                                    <input type="text" class="form-control mat-name" name="materials[]"
                                           value="@isset($materials->materials[0]){{ $materials->materials[0] }}@elseif(isset(old('materials')[0])){{ old('materials')[0] }}@endif"
                                           placeholder="Inserisci materiale">

                                    <div class="input-group-append">
                                        <input type="text" class="form-control" name="material_qty[]"
                                               value="@isset($materials->qty[0]){{ $materials->qty[0] }}@elseif(isset(old('material_qty')[0])){{ old('material_qty')[0] }}@endif"
                                               placeholder="Quantità" style="max-width: 200px !important;">

                                        <select name="base[]" class="form-control" style="max-width: 150px !important;">
                                            <option value="" @if(!isset($materials) or !isset($materials->base[0])){{ 'selected' }}@endif>Base</option>
                                            <option value="acquosa" @if((isset($materials->base) and 'acquosa' == $materials->base[0]) or
                                                                        (isset(old('base')[0]) and 'acquosa' == old('base')[0])){{ 'selected' }}@endif>Acquosa</option>
                                            <option value="solvente" @if((isset($materials->base) and 'solvente' == $materials->base[0]) or
                                                                         (isset(old('base')[0]) and 'solvente' == old('base')[0])){{ 'selected' }}@endif>Solvente</option>
                                        </select>
                                    </div>
                                    <div class="input-group-append">
                                        <span  class="input-group-text add-materials">
                                            <i class="fa fa-plus-circle"></i>
                                        </span>
                                    </div>
                                </div>

                                @if(isset($materials) and isset($materials->materials) and isset($materials->qty))
                                    @for($m=1; $m<count($materials->materials); $m++)
                                        @if(isset($materials->materials[$m]) and isset($materials->qty[$m]))
                                        @component('backend.components.building-site-materials-table-row', [
                                            'materialName' => $materials->materials[$m],
                                            'materialQty' => $materials->qty[$m],
                                            'materialBase' => (isset($materials->base[$m]) ? $materials->base[$m] : null),
                                        ])@endcomponent
                                        @endif
                                    @endfor

                                @elseif(null !== old('materials') and null !== old('material_qty'))
                                    @for($m=1; $m<count(old('materials')); $m++)
                                        @if(isset(old('materials')[$m]) and isset(old('materials')[$m]))
                                            @component('backend.components.building-site-materials-table-row', [
                                                'materialName' => old('materials')[$m],
                                                'materialQty' => old('materials')[$m],
                                                'materialBase' => ((null !== old('base') and isset(old('base')[$m])) ? old('base')[$m] : null),
                                            ])@endcomponent
                                        @endif
                                    @endfor
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="button" class="d-block">Seleziona uno o più macchinari o attrezzature</label>

                                <button type="button" class="btn btn-alt-info" data-toggle="modal" data-target="#modal-macchinari">Seleziona Macchinari</button>
                                <button type="button" class="btn btn-alt-warning" data-toggle="modal" data-target="#modal-attrezzature">Seleziona Attrezzature</button>


                                <div class="sel-car-cont mt-3">
                                    @php
                                        if (isset($buildingSite)) {
                                            $selectedMachines = $buildingSite->machineries();
                                        } else if (null !== old('machines')) {
                                            $selectedMachines = \App\Machinery::whereIn('id', old('machines'));
                                        }
                                    @endphp

                                    @isset($selectedMachines)
                                        @foreach($selectedMachines->get() as $machine)
                                            <span class="@if('vehicle' == $machine->machine_type){{ 'cars-banner' }}@else{{ 'tools-banner' }}@endif" id="banner-{{ $machine->id }}">{{ $machine->machine_name }}</span>
                                        @endforeach
                                    @endisset
                                </div>
                            </div>
                        </div>

                        @isset($buildingSite)
                        <div class="form-group row pb-20">
                            <div class="col-12">
                                <div id="dropzonediv" class="dropzone sortable">
                                    <div class="dz-message" data-dz-message><span>Trascina qui le immagini relative al cantiere</span></div>
                                    @foreach($bsMedia as $media)
                                        @component('backend.components.media-preview', [
                                            'mediaFile' => $media,
                                            'showOverlayDelete' => true
                                        ])@endcomponent
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label>Documenti caricati</label>

                                <ul>
                                    @foreach($buildingSite->media('file')->get() as $file)
                                        <li class="media-item">
                                            <a href="{{ route('download_media_file', $file->id) }}">{{ $file->media_name }} ({{ $file->extension }})</a> -
                                            <a href="#" data-url="{{ route('media.destroy', $file->id) }}"
                                               data-element-name="file"
                                               data-element-type="questo"
                                               class="delete-media" style="color:red">Elimina</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <label>Note cantiere</label>

                                <ul>
                                    @foreach($buildingSite->notes()->get() as $note)
                                        <li>
                                            <a href="{{ route('view_note', $note->id) }}">Nota del {{ $note->created_at }} di {{ $note->created_by }}</a> -
                                            <a href="#" data-url="{{ route('notes.destroy', $note->id) }}"
                                               data-top-element="li"
                                               class="delete-row-btn" style="color:red">Elimina</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endisset

                        <div class="block">
                        </div>

                        <div class="form-group row">
                            @isset($buildingSite)
                            <div class="col-6 btn-left">
                                <button type="button" data-toggle="modal" data-target="#chiudi-commessa" class="btn btn-alt-primary">
                                    <i class="fa fa-archive mr-5"></i> Chiudi commessa
                                </button>
                            </div>
                            @endisset

                            <div class="col-6 btn-right">
                                <a href="{{ route('building-sites.index') }}" class="btn btn-alt-secondary">
                                    <i class="fa fa-remove mr-5"></i> Annulla
                                </a>

                                <button type="submit" class="btn btn-success fes-btn-w">
                                    <i class="fa fa-save mr-5"></i> Salva
                                </button>
                            </div>
                        </div>


                        {{-- macchinari modal start --}}
                        <div class="modal fade" id="modal-macchinari" tabindex="-1" role="dialog" aria-labelledby="modal-macchinari" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-popin" role="document" style="max-width: 80%;">
                                <div class="modal-content">
                                    <div class="block block-themed block-transparent mb-0">
                                        <div class="block-header bg-primary-dark">
                                            <h3 class="block-title">Seleziona uno o più macchinari o attrezzature</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                    <i class="si si-close"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row multiselect-content">

                                            @foreach($machinery as $m)
                                                <div class="col-md-12 col-lg-6">
                                                    <div class="cars-precomp-info">
                                                        <input type="checkbox" class="cars-precomp-check vehicle" id="machine-{{ $m->id }}"
                                                               name="machines[]"
                                                               @if((isset($selectedMachines) and in_array($m->id, $selectedMachines->pluck('id')->toArray())) or
                                                                    (null !== old('machines') and in_array($m->id, old('machines'))))
                                                                   checked
                                                               @endif
                                                               value="{{ $m->id }}"
                                                               data-name="{{ $m->machine_name }}">

                                                        <label for="machine-{{ $m->id }}" class="w-100">
                                                            <div>
                                                                @if(null !== $m->mainImage())
                                                                <img src="{{ $m->mainImage()->getFullPath('thumb') }}" alt="{{ $m->machine_name }}">
                                                                @else
                                                                <img src="{{ asset('backend/images/machinery-no-image.jpg') }}" alt="">
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <p><span class="font-weight-bold">Macchinario: </span>{{ $m->machine_name }}</p>
                                                                <p><span class="font-weight-bold">Numero: </span>{{ $m->machine_number }}</p>
                                                                <p><span class="font-weight-bold">Descrizione: </span>{{ substr($m->machine_description, 0, 80) }}</p>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Chiudi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- macchinari modal start  end --}}



                        {{-- attrezzature modal start --}}
                        <div class="modal fade" id="modal-attrezzature" tabindex="-1" role="dialog" aria-labelledby="modal-attrezzature" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-popin" role="document" style="max-width: 80%;">
                                <div class="modal-content">
                                    <div class="block block-themed block-transparent mb-0">
                                        <div class="block-header bg-primary-dark">
                                            <h3 class="block-title">Seleziona uno o più macchinari o attrezzature</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                    <i class="si si-close"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row multiselect-content">

                                            @foreach($tools as $m)
                                                <div class="col-md-12 col-lg-6">
                                                    <div class="cars-precomp-info">
                                                        <input type="checkbox" class="cars-precomp-check tool" id="machine-{{ $m->id }}"
                                                               name="machines[]"
                                                               @if((isset($selectedMachines) and in_array($m->id, $selectedMachines->pluck('id')->toArray())) or
                                                                    (null !== old('machines') and in_array($m->id, old('machines'))))
                                                                   checked
                                                               @endif
                                                               value="{{ $m->id }}"
                                                               data-name="{{ $m->machine_name }}">

                                                        <label for="machine-{{ $m->id }}" class="w-100">
                                                            <div>
                                                                @if(null !== $m->mainImage())
                                                                <img src="{{ $m->mainImage()->getFullPath('thumb') }}" alt="{{ $m->machine_name }}">
                                                                @else
                                                                <img src="{{ asset('backend/images/machinery-no-image.jpg') }}" alt="">
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <p><span class="font-weight-bold">Macchinario: </span>{{ $m->machine_name }}</p>
                                                                <p><span class="font-weight-bold">Numero: </span>{{ $m->machine_number }}</p>
                                                                <p><span class="font-weight-bold">Descrizione: </span>{{ substr($m->machine_description, 0, 80) }}</p>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Chiudi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- attrezzature modal start  end --}}


                    </form>
                </div>
            </div>
        </div>
    </div>

    @isset($buildingSite)
    {{-- chiudi commessa modal start --}}
    <div class="modal fade" id="chiudi-commessa" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="{{ route('close_building_site', $buildingSite->id) }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Desideri chiudere questa commessa?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Cliccando sul pulsante "chiudi commessa" questo cantiere verr&agrave; chiuso definitivamente
                        e non potr&agrave; pi&ugrave; essere riaperto.<br /><br />
                            <strong>Tutti i dati connessi a questo cantiere rimarranno disponibili.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-alt-primary">Chiudi commessa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- chiudi commessa modal end --}}
    @endisset

    @include('backend.partials.handlebar-bs-note-tpl')

@endsection

@section('footer')

    @component('backend.components.delete-modal', [
        'recordName' => 'record nota'
    ])@endcomponent

    <script async
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBGXjN7Cd-uQu2X9vND3MPjW1k6cZNtUSk&libraries=places&callback=initAutocomplete&language=it-IT">
    </script>

    <script>
        var placeSearch, autocomplete;
        var componentForm = {
            street_number: 'short_name',
            route: 'long_name',
            locality: 'long_name',
            administrative_area_level_1: 'short_name',
            country: 'long_name',
            postal_code: 'short_name'
        };

        function initAutocomplete() {
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
                {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);

            var input = document.getElementById('autocomplete');
            google.maps.event.addDomListener(input, 'keydown', function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                }
            });
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();

            // save the coordinates in the respective fields
            if (null != lat && null != lng) {
                document.getElementById('lat').value = lat.toString();
                document.getElementById('lng').value = lng.toString();
            }

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }
        }

        // Bias the autocomplete object to the user's geographical location,
        // as supplied by the browser's 'navigator.geolocation' object.
        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }

        function selectAll(){
            $("#employee_visibility").find('option').prop('selected', 'selected').end().select2();
        }
    </script>
    <script src="{{ asset('backend/js/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('backend/js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script>jQuery(function(){ Codebase.helpers(['select2', 'ckeditor']); });</script>
    <script>
        const currentRecord = @isset($buildingSite){{ $buildingSite->id.';' }}@else{{ 'null;' }}@endisset
        const materialList = '{{ route('material_list') }}';
    </script>
    <script src="{{ asset('backend/js/plugins/dropzonejs/dropzone.min.js') }}"></script>
    <script src="{{ asset('backend/js/handlebars.min.js') }}"></script>
    <script src="{{ asset('backend/js/edit-building-site.js') }}?v2"></script>
@endsection