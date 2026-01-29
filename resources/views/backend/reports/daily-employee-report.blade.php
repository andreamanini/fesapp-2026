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
                    <h3 class="block-title font-w700">Rapportino Dipendente</h3>
                </div>
                <div class="block-content">

                    <div class="form-group row no-internet-div">
                        <div class="col-12">
                            <p class="alert alert-danger">La tua connessione internet &egrave; assente, assicurati di non aggiornare questa pagina per non perdere i dati inseriti. <br />
                                Il salvataggio del rapportino verr&agrave; ripristinato non appena il segnale torner&agrave; attivo.
                            </p>
                        </div>
                    </div>

                    <form method="POST" id="daily-report-form"
                        @if (isset($report->id))
                            action="{{ route('update_employee_report', $report->id) }}"
                        @else
                          action="{{ route('store_employee_report') }}"
                        @endif>

                        @csrf

                        @if (isset($report->id)){{ method_field('PATCH') }}@endif

                        <input type="hidden" name="building_site_id" value="{{ $buildingSite->id }}" />
                        <input type="hidden" name="report_view" value="employee" />
                        <input type="hidden" id="location_lat" name="location_lat" value="" />
                        <input type="hidden" id="location_lng" name="location_lng" value="" />

                        @if(isset($report) and 'incomplete' == $report->report_type)
                        <input type="hidden" name="incomplete_report" value="Y" />
                        @endif


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
                            <div class="col-4">
                                <h5>
                                    @isset($report)
                                        {{ $report->employee->name }} {{ $report->employee->surname }}
                                    @else
                                        {{ auth()->user()->name }} {{ auth()->user()->surname }}
                                    @endif
                                </h5>
                            </div>
                            <div class="col-4">
                            </div>
                            <div class="col-4 text-right">
                                <h5>@isset($report){{ $report->created_at }}@else{{ date('d-m-Y') }}@endisset</h5>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <h5>{{ $buildingSite->site_name }}
                                    @if(null !== $buildingSite->address and isset($buildingSite->address->autocomplete))
                                        - {{ $buildingSite->address->autocomplete }}
                                    @endif
                                </h5>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3">
                                <label for="truck_no">* N. Camion</label>
                                <input type="text" class="form-control" id="truck_no" name="truck_no"
                                       value="@if(null !== old('truck_no')){{ old('truck_no') }}@elseif(isset($report)){{ $report->truck_no }}@endif"
                                       placeholder="N.Camion.." required />
                            </div>
                            <div class="col-3">
                                <label for="break_time">* Nome del guidatore</label>
                                {{--<input type="text" class="form-control" id="truck_driver_name" name="truck_driver_name"--}}
                                       {{--value="@if(null !== old('truck_driver_name')){{ old('truck_driver_name') }}@elseif(isset($report)){{ $report->truck_driver_name }}@endif"--}}
                                       {{--placeholder="Nome del guidatore" required />--}}

                                <select name="truck_driver_name" id="truck_driver_name" class="form-control" required>
                                    <option value=""></option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->name }} {{ $employee->surname }}"
                                                @if(old('truck_driver_name') == $employee->name . ' '. $employee->surname)
                                                    {{ 'selected' }}
                                                @elseif(isset($report) and $report->truck_driver_name == $employee->name . ' '. $employee->surname)
                                                    {{ 'selected' }}
                                                @endif
                                        >
                                            {{ $employee->name }} {{ $employee->surname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="total_break_time">* Orario di inizio lavoro</label>
                                <input type="hidden" id="date_time_start" name="date_time_start" value="" />
                                <input type="text" class="js-masked-time form-control" id="time_start"
                                       name="time_start"
                                       value="@if(null !== old('time_start')){{ old('time_start') }}@elseif(isset($report)){{ $report->transformDateField('time_start', 'H:i') }}@endif"
                                       placeholder="00:00" required />
                            </div>
                            <div class="col-3">
                                <label for="total_break_time">* Orario di fine lavoro</label>
                                <input type="hidden" id="date_time_end" name="date_time_end" value="" />
                                <input type="text" class="js-masked-time form-control" id="time_end"
                                       name="time_end"
                                       value="@if(null !== old('time_end')){{ old('time_end') }}@elseif(isset($report)){{ $report->transformDateField('time_end', 'H:i') }}@endif"
                                       placeholder="00:00" required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3">
                                <label for="travel_time">* Totale ore viaggio <i class="fa fa-info-circle"></i></label>
                                <input type="number" class="form-control replacecomma" id="travel_time" name="travel_time"
                                       step=".25"
                                       value="@if(null !== old('travel_time')){{ old('travel_time') }}@elseif(isset($report)){{ $report->travel_time }}@endif"
                                       placeholder="Totale ore viaggio.." required/>

                            </div>
                            <div class="col-3">
                                <label for="meals_no">* N. pasti e totale speso</label>
                                <input type="text" class="form-control" id="meals_no" name="meals_no"
                                       value="@if(null !== old('meals_no')){{ old('meals_no') }}@elseif(isset($report)){{ $report->meals_no }}@endif"
                                       placeholder="N. pasti e totale speso" required/>
                            </div>
                            <div class="col-3">
                                <label for="break_time">* Totale ore pausa <i class="fa fa-info-circle"></i></label>
                                <input type="number" class="form-control replacecomma" id="total_break_time" name="total_break_time"
                                       step=".25"
                                       value="@if(null !== old('total_break_time')){{ old('total_break_time') }}@elseif(isset($report)){{ $report->total_break_time }}@endif"
                                       placeholder="Totale ore pausa" required/>
                            </div>
                            <div class="col-3">
                                <label for="break_from_to">* Orario di pausa</label>
                                <input type="text" class="form-control" id="break_from_to" name="break_from_to"
                                       value="@if(null !== old('break_from_to')){{ old('break_from_to') }}@elseif(isset($report)){{ $report->break_from_to }}@endif"
                                       placeholder="Dalle ore .. alle ore .." required/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <p><i class="fa fa-info-circle"></i>
                                    Inserisci 0.25 per 15 minuti, oppure 1.5 per 1h e 30 min o 1.75 per 1 ora e 45 min. Incrementi di 0.25 sono uguali a 15 minuti</p>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group row">
                            <div class="col-12">
                                <h6  class="h-number-compil">1</h6><h6 class="h-compil">* OPERAI IN CANTIERE</h6>
                            </div>
                                @php
                                $userid = auth()->user()->id;
                                @endphp
                                <div class="col-12">
                                    <div class="input-group">
                                        <select class="js-select2 form-control" id="employee_visibility" name="employees[]"
                                                style="width: 100%;" data-placeholder="Seleziona uno o più dipendenti" multiple>
                                            <option></option> {{-- mantenere questo tag serve per il funzionamento del select2 --}}
                                            @if(null !== old('employees') or isset($report))
                                                @php
                                                    $emplArray = (null !== old('employees') ? old('employees') : json_decode($report->employees));
                                                @endphp
                                            @else
                                                @php
                                                    $emplArray = array();
                                                @endphp
                                            @endif
                                            
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->name }} {{ $employee->surname }}" @php if($userid == $employee->id OR in_array($employee->name." ".$employee->surname,$emplArray)) echo 'selected' ; @endphp  >{{ $employee->name }} {{ $employee->surname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>



                        </div>

                        <!--
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="shift_type">* Tipo di turno</label>
                                <select class="form-control" id="shift_type" name="shift_type" required>
                                    <option value="">Seleziona il tipo di turno</option>
                                    <option value="diurno">Diurno (00:01 - 24:00)</option>
                                    <option value="notturno">Notturno (12:01 - 12:00 del giorno successivo)</option>
                                </select>
                            </div>
                        </div> -->

                        <select class="form-control" id="shift_type" name="shift_type" required>
                            <option value="">Seleziona il tipo di turno</option>
                            <option value="diurno" {{ old('shift_type', isset($report) ? $report->shift_type : null) == 'diurno' ? 'selected' : '' }}>
                                Diurno (00:01 - 24:00)
                            </option>
                            <option value="notturno" {{ old('shift_type', isset($report) ? $report->shift_type : null) == 'notturno' ? 'selected' : '' }}>
                                Notturno (12:01 - 12:00 del giorno successivo)
                            </option>
                            
                            
                        </select>

                        <hr>
                        <div class="form-group row row-descrizione-lavoro">
                            <div class="col-12">
                                <h6  class="h-number-compil">2</h6><h6 class="h-compil">* DESCRIZIONE LAVORO</h6>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_coperture">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_coperture"
                                           name="job_coperture"
                                           data-detail-attr="Coperture"
                                           @if((1 == old('job_coperture')) or (isset($report) and !empty($jobDetails['job_coperture']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Coperture
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_stuccature">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_stuccature"
                                           name="job_stuccature"
                                           data-detail-attr="Stuccature"
                                           @if((1 == old('job_stuccature')) or (isset($report) and !empty($jobDetails['job_stuccature']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Stuccature
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_carteggiatura">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_carteggiatura"
                                           name="job_carteggiatura"
                                           data-detail-attr="Carteggiature"
                                           @if((1 == old('job_carteggiatura')) or (isset($report) and !empty($jobDetails['job_carteggiatura']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Carteggiature
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_lavaggio">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_lavaggio"
                                           name="job_lavaggio"
                                           data-detail-attr="Lavaggio"
                                           @if((1 == old('job_lavaggio')) or (isset($report) and !empty($jobDetails['job_lavaggio']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Lavaggio
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_sabbiatura">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_sabbiatura"
                                           name="job_sabbiatura"
                                           data-detail-attr="Sabbiatura"
                                           @if((1 == old('job_sabbiatura')) or (isset($report) and !empty($jobDetails['job_sabbiatura']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Sabbiatura
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_verniciatura">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_verniciatura"
                                           name="job_verniciatura"
                                           data-detail-attr="Verniciatura"
                                           @if((1 == old('job_verniciatura')) or (isset($report) and !empty($jobDetails['job_verniciatura']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Verniciatura
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_intonaco">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_intonaco"
                                           name="job_intonaco"
                                           data-detail-attr="Intonaco"
                                           @if((1 == old('job_intonaco')) or (isset($report) and !empty($jobDetails['job_intonaco']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Intonaco
                                </label>
                            </div>

                            <div class="col-sm-8 col-md-9 offset-sm-4 offset-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_other">
                                    <input type="checkbox" class="css-control-input" id="job_other"
                                           name="job_other"
                                           data-detail-attr="Altro"
                                           @if((1 == old('job_other')) or (isset($report) and !empty($jobDetails['job_other']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Altro
                                </label>
                                <input style="display: inline-block;width: 85%;" type="text" class="form-control activate_checkbox"
                                       id="job_other" name="job_other_text"
                                       data-detail-attr="Altro"
                                       data-target-check="job_othercheck"
                                       placeholder="Altro.. aggiungi specifiche"
                                       value="@if(null !== old('job_other_text')){{ old('job_other_text') }}@elseif(isset($report) and !empty($jobDetails['job_other_text'])){{ $jobDetails['job_other_text'] }}@endif" />
                            </div>

                        </div>

                        @php
                            $arrayJobDetails = [];

                            if (null !== old('job_coperture') or !empty($jobDetails['job_coperture'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Coperture', 'field_name' => 'job_coperture' ]);
                            }

                            if (null !== old('job_stuccature') or !empty($jobDetails['job_stuccature'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Stuccature', 'field_name' => 'job_stuccature' ]);
                            }

                            if (null !== old('job_carteggiatura') or !empty($jobDetails['job_carteggiatura'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Carteggiature', 'field_name' => 'job_carteggiatura' ]);
                            }

                            if (null !== old('job_lavaggio') or !empty($jobDetails['job_lavaggio'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Lavaggio', 'field_name' => 'job_lavaggio' ]);
                            }

                            if (null !== old('job_sabbiatura') or !empty($jobDetails['job_sabbiatura'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Sabbiatura', 'field_name' => 'job_sabbiatura' ]);
                            }

                            if (null !== old('job_verniciatura') or !empty($jobDetails['job_verniciatura'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Verniciatura', 'field_name' => 'job_verniciatura' ]);
                            }

                            if (null !== old('job_intonaco') or !empty($jobDetails['job_intonaco'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Intonaco', 'field_name' => 'job_intonaco' ]);
                            }

                            if (null !== old('job_other') or !empty($jobDetails['job_other'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Altro', 'field_name' => 'job_other_text' ]);
                            }
                        @endphp
                        {{-- this row will only become visible if a job has been selected by the user --}}
                        <div class="form-group row" id="job-hour-details-row" style="@if(count($arrayJobDetails) == 0){{ 'display: none;' }}@endif">
                            @foreach($arrayJobDetails as $jd)
                                @php
                                if (null !== old("{$jd['field_name']}_details")) {
                                    $value = old("{$jd['field_name']}_details");

                                } elseif(!empty($jobDetails['job_other'])) {
                                    $value = $jobDetails['job_other'];

                                } elseif(!empty($jobDetails["{$jd['field_name']}"])) {
                                    $value = $jobDetails["{$jd['field_name']}"];
                                } else {
                                    $value = '';
                                }
                                @endphp
                                <div class="col-lg-4 col-md-12 jb-det-Coperture">
                                    <label for="job-details-{{ $loop->index }}" class="jb-det-{{ $jd['name'] }}">
                                        * Inserisci le ore lavorate per {{ $jd['name'] }}
                                        <i class="fa fa-info-circle"></i>
                                    </label>
                                    <input type="number" min=".25" class="form-control mb-15 job-split-hours replacecomma jb-det-{{ $jd['name'] }}" id="job-details-{{ $loop->index }}"
                                           name="{{ $jd['field_name'] }}_details"
                                           value="{{ $value }}"
                                           step=".25"
                                           placeholder="Inserisci le ore lavorate per {{ $jd['name'] }}"
                                           required="required" />
                                </div>
                            @endforeach
                        </div>
                        {{-- end job hour details --}}
                        
                        <div class="form-group row row-descrizione-lavoro">

                            <div class="col-12">
                                <div class="form-group">
                                    <label style="margin-top: 10px;" for="work_description">* Descrizione lavori eseguiti</label>
                                    <textarea required class="form-control" id="work_description"
                                              name="work_description" rows="8">@if(null !== old('work_description')){{ old('work_description') }}@elseif(isset($report)){{ $report->work_description }}@endif</textarea>
                                </div>
                            </div>
                            <!--
                            @if(!isset($report) or (isset($report)))

                                @if(isset($report))
                                    @php
                                        $reportMqArray = $report->rows()->get();
                                    @endphp
                                @endif
                            <div class="col-12">
                                <div class="no-more-tables">
                                    <table class="table table-striped table-sabb-vern">
                                        <thead>
                                        <tr class="row-header">
                                            <td>TIPO LAVORO</td>
                                            <td>STRUTTURA</td>
                                            <td>MATERIALE</td>
                                            <td class="table-qty">QUANTITÀ</td>
                                            <td>MISURA (in metri)</td>
                                            <td>TOTALE</td>
                                            <td></td>
                                        </tr>
                                        </thead>
                                        <tbody id="sqmt-table-body">

                                        @component('backend.components.daily-report-mq-row',[
                                            'struttV' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_verniciata : null),
                                            'struttS' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_verniciata : null),
                                            'struttLavaggio' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_lavaggio : null),
                                            'struttSoffiatura' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_soffiatura : null),
                                            'struttIntonaco' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_intonaco : null),
                                            'material' => (isset($reportMqArray[0]) ? $reportMqArray[0]->materiale : null),
                                            'qty' => (isset($reportMqArray[0]) ? $reportMqArray[0]->qty : null),
                                            'mqX' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_x : null),
                                            'mqY' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_y : null),
                                            'mqZ' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_z : null),
                                            'mqTot' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_tot : null),
                                            'workType' => (isset($reportMqArray[0]) ? $reportMqArray[0]->work_type : null),
                                            'idRow' => 0
                                        ])@endcomponent

                                        @if(isset($reportMqArray) and $reportMqArray->count() > 1)
                                            @for($i=1; $i<$reportMqArray->count(); $i++)
                                                @component('backend.components.daily-report-mq-row',[
                                                    'struttV' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_verniciata : null),
                                                    'struttS' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_verniciata : null),
                                                    'struttLavaggio' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_lavaggio : null),
                                                    'struttSoffiatura' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_soffiatura : null),
                                                    'struttIntonaco' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_intonaco : null),
                                                    'material' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->materiale : null),
                                                    'qty' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->qty : null),
                                                    'mqX' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_x : null),
                                                    'mqY' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_y : null),
                                                    'mqZ' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_z : null),
                                                    'mqTot' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_tot : null),
                                                    'workType' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->work_type : null),
                                                    'showRemoveBtn' => true,
                                                    'idRow' => $i
                                                ])@endcomponent
                                            @endfor
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                                <div class="col-12 mb-4">
                                    <div class="no-more-tables">
                                        <table class="table table-striped table-sabb-vern fine-cantiere">
                                            <thead>
                                            <tr class="row-header">
                                                <td>RIEPILOGO LAVORI</td>
                                                <td>TOTALE</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Strutture sabbiate</td>
                                                <td>{{ $totSabbiato }} mq</td>
                                            </tr>
                                            <tr>
                                                <td>Strutture verniciate</td>
                                                <td>{{ $totVerniciato }} mq</td>
                                            </tr>
                                            <tr>
                                                <td>Altro</td>
                                                <td>{{ $totAltro }} mq</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            -->
                        </div>
                       
                        <hr>

                        <div class="form-group row row-attrezzatura">
                            <div class="col-12">
                                <h6 class="h-number-compil">3</h6><h6 class="h-compil">* ATTREZZATURA</h6>
                            </div>
                        
                            @php
                                $jobEquipment = $jobEquipment ?? [];
                            @endphp
                        
                            @foreach([
                                'equipment_idropulitrici' => 'Idropulitrici N°',
                                'equipment_intonacatrici' => 'Intonacatrici N°',
                                'equipment_airmix' => 'Airmix N°',
                                'equipment_airless' => 'Airless N°',
                                'equipment_ponteggi' => 'Ponteggi Colore',
                                'equipment_scale_colore' => 'Scale Colore',
                                'equipment_carteggiatrici' => 'Carteggiatrici',
                                'equipment_strumentazione' => 'Strumentazione'
                            ] as $key => $label)
                                <div class="col-6 col-sm-4">
                                    <label class="css-control css-control-warning css-checkbox">
                                        <input type="checkbox" class="css-control-input"
                                               @if(old($key) !== null || (isset($report) && isset($jobEquipment[$key]) && !empty($jobEquipment[$key]))) checked @endif
                                               id="check_{{ $key }}" />
                                        <span class="css-control-indicator"></span>
                                    </label>
                                    <div style="display: inline-block; width: 85%;">
                                        <label for="{{ $key }}">{{ $label }}</label>
                                        <input type="text" class="form-control activate_checkbox" id="{{ $key }}"
                                               name="{{ $key }}" data-target-check="check_{{ $key }}"
                                               value="{{ old($key, $jobEquipment[$key] ?? '') }}"
                                               placeholder="{{ $label }}" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        

                        <hr>

                        <div class="form-group row">
                            <div class="col-12">
                                <h6  class="h-number-compil">4</h6><h6 class="h-compil">LAVORI EXTRA</h6>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea class="form-control" id="extra_work_description"
                                              name="extra_work_description" rows="8">@if(null !== old('extra_work_description')){{ old('extra_work_description') }}@elseif(isset($report)){{ $report->extra_work_description }}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="time_lost">Tempo perso / Ore di fermo</label>
                                    <input type="text" class="form-control" id="time_lost" name="time_lost"
                                           value="@if(null !== old('time_lost')){{ old('time_lost') }}@elseif(isset($report)){{ $report->time_lost }}@endif"
                                           placeholder="Tempo perso / Ore di fermo" />
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row row-materiali">
                            <div class="col-12">
                                <h6 class="h-number-compil">5</h6><h6 class="h-compil">* MATERIALI</h6>
                            </div>
                        
                            <div id="gasolio-camion-warning" class="col-12 d-none">
                                <div class="alert alert-warning">
                                    <p>Hai impostato, <strong>te stesso</strong> come guidatore del camion, assicurati quindi di specificare i <strong>litri gasolio camion utilizzati per questa tratta</strong>.</p>
                                </div>
                            </div>
                        
                            @php
                                $jobMaterials = $jobMaterials ?? [];
                            @endphp
                        
                            @foreach([
                                'materials_gasolio_camion' => ['label' => 'Gasolio camion Lt', 'type' => 'number', 'step' => '.5'],
                                'materials_gasolio_compressore' => ['label' => 'Gasolio compressore Lt', 'type' => 'number', 'step' => '.5'],
                                'materials_gasolio_altro' => ['label' => 'Gasolio altro Lt', 'type' => 'number', 'step' => '.5'],
                                'materials_diluente' => ['label' => 'Diluente Lt', 'type' => 'text'],
                                'materials_intonacatrici' => ['label' => 'Intonacatrici N', 'type' => 'text'],
                                'materials_big_bag' => ['label' => 'Big bag N', 'type' => 'text'],
                                'materials_km_giornalieri' => ['label' => 'Km Giornalieri', 'type' => 'text']
                            ] as $key => $data)
                                <div class="col-6 col-lg-4">
                                    <label class="css-control css-control-warning css-checkbox">
                                        <input type="checkbox" class="css-control-input"
                                               @if(old($key) !== null || (isset($report) && isset($jobMaterials[$key]) && !empty($jobMaterials[$key]))) checked @endif
                                               id="check_{{ $key }}" />
                                        <span class="css-control-indicator"></span>
                                    </label>
                                    <div style="display: inline-block; width: 85%;">
                                        <label for="{{ $key }}">{{ $data['label'] }}</label>
                                        <input type="{{ $data['type'] }}" class="form-control activate_checkbox" id="{{ $key }}"
                                               name="{{ $key }}" data-target-check="check_{{ $key }}"
                                               value="{{ old($key, $jobMaterials[$key] ?? '') }}"
                                               placeholder="{{ $data['label'] }}"
                                               @if(isset($data['step'])) step="{{ $data['step'] }}" min="0" @endif />
                                    </div>
                                </div>
                            @endforeach
                        
                            @foreach([
                                'materials_sacchi' => ['label' => 'Sacchi N', 'descr_label' => 'Descrizione'],
                                'materials_latte' => ['label' => 'Latte N', 'descr_label' => 'Descrizione']
                            ] as $key => $data)
                                <div class="col-12">
                                    <label class="css-control css-control-warning css-checkbox">
                                        <input type="checkbox" class="css-control-input"
                                               @if(old($key) !== null || (isset($report) && (!empty($jobMaterials[$key]) || !empty($jobMaterials[$key . '_descr'])))) checked @endif
                                               id="check_{{ $key }}" />
                                        <span class="css-control-indicator"></span>
                                    </label>
                                    <div style="display: inline-block; width: 15%;">
                                        <label for="{{ $key }}">{{ $data['label'] }}</label>
                                        <input type="text" class="form-control activate_checkbox" id="{{ $key }}" name="{{ $key }}"
                                               value="{{ old($key, $jobMaterials[$key] ?? '') }}"
                                               data-target-check="check_{{ $key }}"
                                               placeholder="{{ $data['label'] }}" />
                                    </div>
                                    <div class="inp-descr-comp">
                                        <label for="{{ $key }}_descr">{{ $data['descr_label'] }}</label>
                                        <input type="text" class="form-control activate_checkbox" id="{{ $key }}_descr" name="{{ $key }}_descr"
                                               value="{{ old($key . '_descr', $jobMaterials[$key . '_descr'] ?? '') }}"
                                               data-target-check="check_{{ $key }}"
                                               placeholder="{{ $data['descr_label'] }}" />
                                    </div>
                                </div>
                            @endforeach
                        
                            <div class="col-6">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if(old('materials_other') !== null || (isset($report) && !empty($jobMaterials['materials_other']))) checked @endif
                                           id="check_materials_other" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div class="inp-altro-comp">
                                    <label for="materials_other">Altro</label>
                                    <input type="text" class="form-control activate_checkbox" id="materials_other" name="materials_other"
                                           value="{{ old('materials_other', $jobMaterials['materials_other'] ?? '') }}"
                                           data-target-check="check_materials_other"
                                           placeholder="Altro.." />
                                </div>
                            </div>
                        
                            <div class="col-6">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if(old('extra_expenses') !== null || (isset($report) && !empty($report->extra_expenses))) checked @endif
                                           id="check_extra_expenses" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div class="inp-altro-comp">
                                    <label for="extra_expenses">Spese Extra</label>
                                    <input type="number" class="form-control activate_checkbox" id="extra_expenses" name="extra_expenses"
                                           value="{{ old('extra_expenses', $report->extra_expenses ?? '') }}"
                                           data-target-check="check_extra_expenses"
                                           placeholder="Spese Extra.." />
                                </div>
                            </div>
                        </div>                        

                        <div class="form-group row no-internet-div">
                            <div class="col-12">
                                <p class="alert alert-danger">La tua connessione internet &egrave; assente, assicurati di non aggiornare questa pagina per non perdere i dati inseriti. <br />
                                    Il salvataggio del rapportino verr&agrave; ripristinato non appena il segnale torner&agrave; attivo.
                                </p>
                            </div>
                        </div>


                        <div class="form-group row" id="save-form-div">
                            <div class="col-12 agree-sign-rapp">
                                <p><small class="font-weight-bold">*attenzione! Cliccando il tasto salva, si firma e sottoscrive il presente foglio </small></p>
                            </div>
                            <div class="col-12 btn-right">
                                <a href="{{ route('building-sites.show', $buildingSite->id) }}" class="btn btn-alt-secondary">
                                    <i class="fa fa-remove mr-5"></i> Annulla
                                </a>

                                <button type="submit" class="btn btn-success fes-btn-w">
                                    <i class="fa fa-save mr-5"></i> Salva
                                </button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('backend.partials.handlebar-daily-report-sq-mt-tpl')

@endsection

@section('footer')
    <script src="{{ asset('backend/js/plugins/select2/js/select2.min.js') }}"></script>
    <script>jQuery(function(){ Codebase.helpers(['select2', 'ckeditor']); });</script>
    <script src="{{ asset('backend/js/plugins/dropzonejs/dropzone.min.js') }}"></script>
    
    <script src="{{ asset('backend/js/plugins/masked-inputs/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('backend/js/handlebars.min.js') }}"></script>
    <script src="{{ asset('backend/js/moment.js') }}?v2"></script>
    <script src="{{ asset('backend/js/daily-report.js') }}?v2"></script>
    <script>

        // richiedi posizione gps
        getLocation();

        //$(document).ready(function() {
            // Aggiungi un listener sull'input del campo di testo
            $('input[name="job_other_text"]').on('input', function() {
              // Prendi il valore corrente del campo di testo
              var inputValue = $(this).val();
              // Se il valore non è vuoto, seleziona la checkbox
              if (inputValue.trim() !== '') {
                $('input[type="checkbox"][id="job_other"]').prop('checked', true);
              } else {
                // Se il valore è vuoto, deseleziona la checkbox
                $('input[type="checkbox"][id="job_other"]').prop('checked', false);
              }
            });
        //});

        // Check if the user is online or not
        setInterval(function() {
            if (!navigator.onLine) {
                $('.no-internet-div').show();
                $('#save-form-div').hide();
            } else {
                $('.no-internet-div').hide();
                $('#save-form-div').show();
            }
        }, 1000);

        jQuery(function(){ Codebase.helpers(['masked-inputs']); });

        const truckDriverName = '{{ auth()->user()->name }} {{ auth()->user()->surname }}';

        $(document).ready(function() {

// Check the time start and end at the form submission
$('#daily-report-form').submit(function(e) {
    e.preventDefault();

    const shiftType = $('#shift_type').val(); // Tipo di turno (diurno o notturno)
    const timeStart = $('#time_start').val(); // Orario di inizio lavoro
    const timeEnd = $('#time_end').val(); // Orario di fine lavoro

    const startMoment = moment(timeStart, 'HH:mm'); // Parsing dell'orario di inizio in formato 24h
    const endMoment = moment(timeEnd, 'HH:mm'); // Parsing dell'orario di fine in formato 24h

    // Controllo formato orario
    if (!startMoment.isValid()) {
        alert('Il formato dell\'orario di inizio deve essere specificato in hh:mm (esempio: 08:15).');
        return false;
    }

    if (!endMoment.isValid()) {
        alert('Il formato dell\'orario di fine deve essere specificato in hh:mm (esempio: 18:45).');
        return false;
    }

    // Verifica del turno diurno
    if (shiftType === 'diurno') {
        if (startMoment.hours() < 0 || startMoment.hours() >= 24 || endMoment.hours() < 0 || endMoment.hours() >= 24) {
            alert('Per il turno diurno, l\'orario deve essere compreso tra le 00:01 e le 24:00.');
            return false;
        }

        if (!endMoment.isAfter(startMoment)) {
            alert('L\'orario di fine deve essere successivo all\'orario di inizio per il turno diurno.');
            return false;
        }
    }

    // Verifica del turno notturno
    if (shiftType === 'notturno') {

        // Se l'orario di inizio è prima delle 12:01 e l'orario di fine è maggiore o uguale a 12:00 del giorno successivo
        if (startMoment.hours() < 12 && endMoment.hours() >= 12) {
            alert('Per il turno notturno, l\'orario deve iniziare dalle 12:01 e finire entro le 12:00 del giorno successivo.');
            return false;
        }
    }

    // Calcolo delle ore lavorate
    const workedHours = (endMoment.diff(startMoment, 'seconds') / 3600) - ($('#total_break_time').val() ? parseFloat($('#total_break_time').val()) : 0);

    // Controllare che il totale ore lavoro corrisponda al totale ore inserito nella descrizione lavoro
    var partialHours = 0;
    $('.job-split-hours').each(function () {
        if ($(this).val()) {
            partialHours = partialHours + parseFloat($(this).val());
        }
    });

    if (partialHours != workedHours && shiftType === 'diurno') {
        alert('Il totale delle ore lavorate: (' + workedHours.toFixed(2) + ') deve essere uguale al totale ore inserite al punto 2 (' + partialHours.toFixed(2) + ').');
        return false;
    }

    // Controllo lunghezza descrizione
    if ($("#work_description").val().length < 60) {
        alert('La "descrizione lavori eseguiti" deve essere lunga almeno 60 caratteri!');
        return false;
    }

    // Check for work type field completion
    if (checkWorkTypeSelectionHasErrors()) {
        alert('Seleziona una tipologia di lavorazione per ogni MQ inserito!');
        return false;
    }

    // Format the date for the database record
    $('#date_time_start').val(startMoment.format('yyyy-MM-DD HH:mm:00').toString());
    $('#date_time_end').val(endMoment.format('yyyy-MM-DD HH:mm:00').toString());

    $('#daily-report-form').unbind('submit');
    $('#daily-report-form').submit();
});

});




    </script>
@endsection
