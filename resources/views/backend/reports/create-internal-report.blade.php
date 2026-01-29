@extends('layouts.fes-app')

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
                          @endif
                    >

                        @csrf

                        @if (isset($report->id)){{ method_field('PATCH') }}@endif

                        <input type="hidden" name="building_site_id" value="1" />
                        <input type="hidden" name="report_view" value="internal" />
                        <input type="hidden" id="location_lat" name="location_lat" value="" />
                        <input type="hidden" id="location_lng" name="location_lng" value="" />


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
                                <h5>Capannone / Deposito FES - Lonato del Garda</h5>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3">
                                <label for="truck_no">* N. Camion</label>
                                <input type="text" class="form-control" id="truck_no" name="truck_no"
                                       value="@if(null !== old('truck_no')){{ old('truck_no') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->truck_no }}@endif"
                                       placeholder="N.Camion.." required />
                            </div>
                            <div class="col-3">
                                <label for="break_time">* Nome del guidatore</label>
                                <input type="text" class="form-control" id="truck_driver_name" name="truck_driver_name"
                                       value="@if(null !== old('truck_driver_name')){{ old('truck_driver_name') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->truck_driver_name }}@endif"
                                       placeholder="Nome del guidatore" required />
                            </div>
                            <div class="col-3">
                                <label for="total_break_time">* Orario di inizio lavoro</label>
                                <input type="hidden" id="date_time_start" name="date_time_start" value="" />
                                <input type="text" class="js-masked-time form-control" id="time_start"
                                       name="time_start"
                                       value="@if(null !== old('time_start')){{ old('time_start') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->transformDateField('time_start', 'H:i') }}@endif"
                                       placeholder="00:00" required />
                            </div>
                            <div class="col-3">
                                <label for="total_break_time">* Orario di fine lavoro</label>
                                <input type="hidden" id="date_time_end" name="date_time_end" value="" />
                                <input type="text" class="js-masked-time form-control" id="time_end"
                                       name="time_end"
                                       value="@if(null !== old('time_end')){{ old('time_end') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->transformDateField('time_end', 'H:i') }}@endif"
                                       placeholder="00:00" required />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3">
                                <label for="travel_time">Totale ore viaggio <i class="fa fa-info-circle"></i></label>
                                <input type="number" class="form-control replacecomma" id="travel_time" name="travel_time"
                                       step=".25"
                                       value="@if(null !== old('travel_time')){{ old('travel_time') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->travel_time }}@endif"
                                       placeholder="Totale ore viaggio.."/>

                            </div>
                            <div class="col-3">
                                <label for="meals_no">N. pasti e totale speso</label>
                                <input type="text" class="form-control" id="meals_no" name="meals_no"
                                       value="@if(null !== old('meals_no')){{ old('meals_no') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->meals_no }}@endif"
                                       placeholder="N. pasti e totale speso"/>
                            </div>
                            <div class="col-3">
                                <label for="break_time">Totale ore pausa <i class="fa fa-info-circle"></i></label>
                                <input type="number" class="form-control replacecomma" id="total_break_time" name="total_break_time"
                                       step=".25"
                                       value="@if(null !== old('total_break_time')){{ old('total_break_time') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->total_break_time }}@endif"
                                       placeholder="Totale ore pausa"/>
                            </div>
                            <div class="col-3">
                                <label for="break_from_to">Orario di pausa</label>
                                <input type="text" class="form-control" id="break_from_to" name="break_from_to"
                                       value="@if(null !== old('break_from_to')){{ old('break_from_to') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->break_from_to }}@endif"
                                       placeholder="Dalle ore .. alle ore .." />
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
                                <h6  class="h-number-compil">1</h6><h6 class="h-compil">* OPERAI AL CAPANNONE</h6>
                            </div>
                            <div class="col-12 row align-items-center">
                                <div class="col-4 working-materials">
                                    <div class="input-group">
                                        {{--<input type="text" class="form-control" id="employee-name"--}}
                                        {{--placeholder="Inserisci nome operaio">--}}
                                        <select class="form-control" id="employee-name">
                                            <option value=""></option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->name }} {{ $employee->surname }}">{{ $employee->name }} {{ $employee->surname }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <span class="input-group-text add-materials" id="add-employee">
                                                <i class="fa fa-plus-circle"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-8" id="employees-container">
                                    @if(null !== old('employees') or isset($report))
                                        @php
                                            $emplArray = (null !== old('employees') ? old('employees') : json_decode($report->employees));
                                        @endphp
                                        @foreach($emplArray as $employee)
                                            <span class="comp-operaio mr-5">
                                            {{ $employee }}
                                                <i class="fa fa-remove remove-employee" data-target="employee-{{ $loop->index }}"></i>
                                        </span>
                                            <input type="hidden" name="employees[]" id="employee-{{ $loop->index }}" value="{{ $employee }}" />
                                        @endforeach
                                    @else
                                        @php
                                            $random = \Illuminate\Support\Str::random(5);
                                        @endphp
                                        <span class="comp-operaio mr-5">
                                    {{ auth()->user()->name }} {{ auth()->user()->surname }}
                                            <input type="hidden" name="employees[]" id="employee-{{ $random }}" value="{{ auth()->user()->name }} {{ auth()->user()->surname }}" />
                                    @endif
                                </div>
                            </div>


                        </div>

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
                                           @if((1 == old('job_coperture')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_coperture']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Coperture
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_stuccature">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_stuccature"
                                           name="job_stuccature"
                                           data-detail-attr="Stuccature"
                                           @if((1 == old('job_stuccature')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_stuccature']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Stuccature
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_carteggiatura">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_carteggiatura"
                                           name="job_carteggiatura"
                                           data-detail-attr="Carteggiature"
                                           @if((1 == old('job_carteggiatura')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_carteggiatura']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Carteggiature
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_lavaggio">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_lavaggio"
                                           name="job_lavaggio"
                                           data-detail-attr="Lavaggio"
                                           @if((1 == old('job_lavaggio')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_lavaggio']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Lavaggio
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_sabbiatura">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_sabbiatura"
                                           name="job_sabbiatura"
                                           data-detail-attr="Sabbiatura"
                                           @if((1 == old('job_sabbiatura')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_sabbiatura']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Sabbiatura
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_verniciatura">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_verniciatura"
                                           name="job_verniciatura"
                                           data-detail-attr="Verniciatura"
                                           @if((1 == old('job_verniciatura')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_verniciatura']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Verniciatura
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_discarica">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_discarica"
                                           name="job_discarica"
                                           data-detail-attr="Discarica"
                                           @if((1 == old('job_discarica')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_discarica']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Discarica
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_lavaggio_camion">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_lavaggio_camion"
                                           name="job_lavaggio_camion"
                                           data-detail-attr="Lavaggio camion ed attrezzatura"
                                           @if((1 == old('job_lavaggio_camion')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_lavaggio_camion']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Lavaggio camion ed attrezzatura
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_ritiro_materiale">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_ritiro_materiale"
                                           name="job_ritiro_materiale"
                                           data-detail-attr="Ritiro materiale"
                                           @if((1 == old('job_ritiro_materiale')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_ritiro_materiale']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Ritiro materiale
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_ordine_dentro_capannone">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_ordine_dentro_capannone"
                                           name="job_ordine_dentro_capannone"
                                           data-detail-attr="Ordine dentro capannone"
                                           @if((1 == old('job_ordine_dentro_capannone')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_ordine_dentro_capannone']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Ordine dentro capannone
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_ordine_fuori_capannone">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_ordine_fuori_capannone"
                                           name="job_ordine_fuori_capannone"
                                           data-detail-attr="Ordine fuori capannone"
                                           @if((1 == old('job_ordine_fuori_capannone')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_ordine_fuori_capannone']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Ordine fuori capannone
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_pulizia">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_pulizia"
                                           name="job_pulizia"
                                           data-detail-attr="Pulizia"
                                           @if((1 == old('job_pulizia')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_pulizia']))){{ 'checked' }}@endif
                                           value="1" />
                                    <span class="css-control-indicator"></span> Pulizia
                                </label>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <label class="css-control css-control-warning css-checkbox" for="job_other">
                                    <input type="checkbox" class="css-control-input requires-hours" id="job_other"
                                           name="job_other"
                                           data-detail-attr="Altro"
                                           @if((1 == old('job_other')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobDetails['job_other']))){{ 'checked' }}@endif
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

                            if (null !== old('job_discarica') or !empty($jobDetails['job_discarica'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Discarica', 'field_name' => 'job_discarica' ]);
                            }

                            if (null !== old('job_lavaggio_camion') or !empty($jobDetails['job_lavaggio_camion'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Lavaggio camion ed attrezzatura', 'field_name' => 'job_lavaggio_camion' ]);
                            }

                            if (null !== old('job_ritiro_materiale') or !empty($jobDetails['job_ritiro_materiale'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Ritiro materiale', 'field_name' => 'job_ritiro_materiale' ]);
                            }

                            if (null !== old('job_ordine_dentro_capannone') or !empty($jobDetails['job_ordine_dentro_capannone'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Ordine dentro capannone', 'field_name' => 'job_ordine_dentro_capannone' ]);
                            }

                            if (null !== old('job_ordine_fuori_capannone') or !empty($jobDetails['job_ordine_fuori_capannone'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Ordine fuori capannone', 'field_name' => 'job_ordine_fuori_capannone' ]);
                            }

                            if (null !== old('job_pulizia') or !empty($jobDetails['job_pulizia'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Pulizia', 'field_name' => 'job_pulizia' ]);
                            }
                            
                            if (null !== old('job_other') or !empty($jobDetails['job_other'])) {
                                array_push($arrayJobDetails, [ 'name' => 'Altro', 'field_name' => 'job_other' ]);
                            }

                        @endphp
                        {{-- this row will only become visible if a job has been selected by the user --}}
                        <div class="form-group row" id="job-hour-details-row" style="@if(count($arrayJobDetails) == 0){{ 'display: none;' }}@endif">
                            @foreach($arrayJobDetails as $jd)
                                @php
                                    if (null !== old("{$jd['field_name']}_details")) {
                                        $value = old("{$jd['field_name']}_details");

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
                                    <label style="margin-top: 10px;" for="work_description">Descrizione lavori eseguiti</label>
                                    <textarea class="form-control" id="work_description"
                                              name="work_description" rows="8">@if(null !== old('work_description')){{ old('work_description') }}@elseif(isset($report) and auth()->user()->isSuperAdmin()){{ $report->work_description }}@endif</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row row-attrezzatura">
                            <div class="col-12">
                                <h6  class="h-number-compil">3</h6><h6 class="h-compil">ATTREZZATURA</h6>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_idropulitrici')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_idropulitrici']))){{ 'checked' }}@endif
                                           id="check_eq_idrop" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_idropulitrici">Idropulitrici N°</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_idropulitrici"
                                           name="equipment_idropulitrici" data-target-check="check_eq_idrop"
                                           value="@if((null !== old('equipment_idropulitrici')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_idropulitrici']))){{ $jobEquipment['equipment_idropulitrici'] }}@endif"
                                           placeholder="Idropulitrici.." />
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_intonacatrici')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_intonacatrici']))){{ 'checked' }}@endif
                                           id="check_eq_intonac" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_intonacatrici">Intonacatrici N°</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_intonacatrici"
                                           name="equipment_intonacatrici" data-target-check="check_eq_intonac"
                                           value="@if((null !== old('equipment_intonacatrici')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_intonacatrici']))){{ $jobEquipment['equipment_intonacatrici'] }}@endif"
                                           placeholder="Intonacatrici.." />
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_airmix')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_airmix']))){{ 'checked' }}@endif
                                           id="check_eq_airmix" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_airmix">Airmix N°</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_airmix"
                                           name="equipment_airmix" data-target-check="check_eq_airmix"
                                           value="@if((null !== old('equipment_airmix')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_airmix']))){{ $jobEquipment['equipment_airmix'] }}@endif"
                                           placeholder="Airmix.." />
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_airless')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_airless']))){{ 'checked' }}@endif
                                           id="check_eq_airless" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_airless">Airless N°</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_airless"
                                           name="equipment_airless" data-target-check="check_eq_airless"
                                           value="@if((null !== old('equipment_airless')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_airless']))){{ $jobEquipment['equipment_airless'] }}@endif"
                                           placeholder="Airless.." />
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_ponteggi')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_ponteggi']))){{ 'checked' }}@endif
                                           id="check_eq_pont" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_ponteggi">Ponteggi Colore</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_ponteggi"
                                           name="equipment_ponteggi" data-target-check="check_eq_pont"
                                           value="@if((null !== old('equipment_ponteggi')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_ponteggi']))){{ $jobEquipment['equipment_ponteggi'] }}@endif"
                                           placeholder="Ponteggi Colore.." />
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_scale_colore')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_scale_colore']))){{ 'checked' }}@endif
                                           id="check_eq_scale" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_scale_colore">Scale Colore</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_scale_colore"
                                           name="equipment_scale_colore" data-target-check="check_eq_scale"
                                           value="@if((null !== old('equipment_scale_colore')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_scale_colore']))){{ $jobEquipment['equipment_scale_colore'] }}@endif"
                                           placeholder="Scale Colore.." />
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_carteggiatrici')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_carteggiatrici']))){{ 'checked' }}@endif
                                           id="check_eq_cart" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_carteggiatrici">Carteggiatrici</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_carteggiatrici"
                                           name="equipment_carteggiatrici" data-target-check="check_eq_cart"
                                           value="@if((null !== old('equipment_carteggiatrici')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_carteggiatrici']))){{ $jobEquipment['equipment_carteggiatrici'] }}@endif"
                                           placeholder="Carteggiatrici.." />
                                </div>
                            </div>
                            
                            <div class="col-6 col-sm-4">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_altro')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_altro']))){{ 'checked' }}@endif
                                           id="check_eq_other" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_altro">Altro</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_altro"
                                           name="equipment_altro" data-target-check="check_eq_other"
                                           value="@if((null !== old('equipment_altro')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_altro']))){{ $jobEquipment['equipment_altro'] }}@endif"
                                           placeholder="Altro.." />
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('equipment_strumentazione')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_strumentazione']))){{ 'checked' }}@endif
                                           id="check_eq_strum" />
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_strumentazione">Strumentazione</label>
                                    <input type="text" class="form-control activate_checkbox" id="equipment_strumentazione"
                                           name="equipment_strumentazione" data-target-check="check_eq_strum"
                                           value="@if((null !== old('equipment_strumentazione')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobEquipment['equipment_strumentazione']))){{ $jobEquipment['equipment_strumentazione'] }}@endif"
                                           placeholder="Strumentazione.." />
                                </div>
                            </div>

                        </div>

                        <hr>

                        <div class="form-group row row-materiali">
                            <div class="col-12">
                                <h6  class="h-number-compil">4</h6><h6 class="h-compil">MATERIALI</h6>
                            </div>

                            <div class="col-6 col-lg-4 ">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('materials_gasolio_camion')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_gasolio_camion']))){{ 'checked' }}@endif
                                           id="gasoliocamioncheckid">
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_gasolio_camion">Gasolio camion Lt</label>
                                    <input type="number" class="form-control activate_checkbox" id="materials_gasolio_camion" name="materials_gasolio_camion"
                                           value="@if((null !== old('materials_gasolio_camion')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_gasolio_camion']))){{ $jobMaterials['materials_gasolio_camion'] }}@endif"
                                           min="0" step=".5"
                                           data-target-check="gasoliocamioncheckid"
                                           placeholder="Gasolio camion Lt.." />
                                </div>
                            </div>

                            <div class="col-6 col-lg-4 ">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('materials_diluente')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_diluente']))){{ 'checked' }}@endif
                                           id="diluentecheckid" >
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_diluente">Diluente Lt</label>
                                    <input type="text" class="form-control activate_checkbox" id="materials_diluente" name="materials_diluente"
                                           value="@if((null !== old('materials_diluente')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_diluente']))){{ $jobMaterials['materials_diluente'] }}@endif"
                                           data-target-check="diluentecheckid"
                                           placeholder="Diluente Lt.." />
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('materials_sacchi')) or (isset($report) and auth()->user()->isSuperAdmin() and (!empty($jobMaterials['materials_sacchi']) or !empty($jobMaterials['materials_sacchi_descr'])))){{ 'checked' }}@endif
                                           id="sacchicheckid" >
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 15%;">
                                    <label for="materials_sacchi">Sacchi N</label>
                                    <input type="text" class="form-control activate_checkbox" id="materials_sacchi" name="materials_sacchi"
                                           value="@if((null !== old('materials_sacchi')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_sacchi']))){{ $jobMaterials['materials_sacchi'] }}@endif"
                                           data-target-check="sacchicheckid"
                                           placeholder="Sacchi N.." />
                                </div>
                                <div class="inp-descr-comp">
                                    <label for="materials_sacchi_descr">Descrizione</label>
                                    <input type="text" class="form-control activate_checkbox" id="materials_sacchi_descr" name="materials_sacchi_descr"
                                           value="@if((null !== old('materials_sacchi_descr')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_sacchi_descr']))){{ $jobMaterials['materials_sacchi_descr'] }}@endif"
                                           data-target-check="sacchicheckid"
                                           placeholder="Descrizione Sacchi.." />
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('materials_latte')) or (isset($report) and auth()->user()->isSuperAdmin() and (!empty($jobMaterials['materials_latte']) or !empty($jobMaterials['materials_latte_descr'])))){{ 'checked' }}@endif
                                           id="lattecheckid" >
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div style="display: inline-block; width: 15%;">
                                    <label for="materials_latte">Latte N</label>
                                    <input type="text" class="form-control activate_checkbox" id="materials_latte" name="materials_latte"
                                           value="@if((null !== old('materials_latte')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_latte']))){{ $jobMaterials['materials_latte'] }}@endif"
                                           data-target-check="lattecheckid"
                                           placeholder="Latte N.." />
                                </div>
                                <div class="inp-descr-comp">
                                    <label for="materials_latte_descr">Descrizione</label>
                                    <input type="text" class="form-control activate_checkbox" id="materials_latte_descr" name="materials_latte_descr"
                                           value="@if((null !== old('materials_latte_descr')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_latte_descr']))){{ $jobMaterials['materials_latte_descr'] }}@endif"
                                           data-target-check="lattecheckid"
                                           placeholder="Descrizione Latte.." />
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('materials_other')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_other']))){{ 'checked' }}@endif
                                           id="othermaterial" >
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div class="inp-altro-comp">
                                    <label for="materials_other">Altro</label>
                                    <input type="text" class="form-control activate_checkbox" id="materials_other" name="materials_other"
                                           value="@if((null !== old('materials_other')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($jobMaterials['materials_other']))){{ $jobMaterials['materials_other'] }}@endif"
                                           data-target-check="othermaterial"
                                           placeholder="Altro.." />
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="css-control css-control-warning css-checkbox">
                                    <input type="checkbox" class="css-control-input"
                                           @if((null !== old('extra_expenses')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($report->extra_expenses))){{ 'checked' }}@endif
                                           id="extrasp" >
                                    <span class="css-control-indicator"></span>
                                </label>
                                <div class="inp-altro-comp">
                                    <label for="extra_expenses">Spese Extra</label>
                                    <input type="number" class="form-control activate_checkbox" id="extra_expenses" name="extra_expenses"
                                           value="@if((null !== old('extra_expenses')) or (isset($report) and auth()->user()->isSuperAdmin() and !empty($report->extra_expenses))){{ $report->extra_expenses }}@endif"
                                           data-target-check="extrasp"
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
                                <a href="" class="btn btn-alt-secondary">
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

@endsection

@section('footer')
    <script src="{{ asset('backend/js/plugins/masked-inputs/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('backend/js/handlebars.min.js') }}"></script>
    <script src="{{ asset('backend/js/moment.js') }}"></script>
    <script>

        // richiedi posizione gps
        getLocation();

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

        $(document).ready(function() {
            // Replace comma on all those inputs that have the class
            $('body').delegate('.replacecomma', 'change', function() {
                $(this).val($(this).val().replace(',', '.'));
            });

            // Add employee function
            $('#add-employee').click(function() {
                if ($('#employee-name').val()) {

                    let r = Math.random().toString(36).substring(7);
                    const operaio = $('<span>', {
                        class: 'comp-operaio mr-5',
                        text: $('#employee-name').val()
                    });

                    const operaioHidden = $('<input />', {
                        type: 'hidden',
                        name: 'employees[]',
                        id: 'employee-'+r,
                        value: $('#employee-name').val()
                    })

                    let removeOperaio = $('<i>', {
                        class: 'fa fa-remove remove-employee',
                    });

                    removeOperaio.attr('data-target', 'employee-'+r);

                    // Add the employee to the form

                    operaio.append(removeOperaio);
                    $('#employees-container').append(operaio);
                    $('#employees-container').append(operaioHidden);

                    // Flush the employee name value
                    $('#employee-name').val('');
                }
            });


            // Remove employee function
            $('#employees-container').delegate('.remove-employee', 'click', function() {
                if ($(this).data('target')) {
                    $('#'+$(this).data('target')).remove();
                    $(this).parent().remove();
                }
            });


            // Adding required hour fields for each job done
            $('.requires-hours').change(function() {
                let detailAttr = $(this).data('detail-attr').replace(/\s+/g, '-').toLowerCase(); // Rimuove spazi e normalizza
                let selector = '.jb-det-' + detailAttr; 

                if ($(this).prop('checked')) {
                    addWorkHourDiv($(this), detailAttr);
                } else {
                    $(selector).remove();

                    // Se non ci sono più campi, nasconde la sezione
                    if ($('#job-hour-details-row').children().length === 0) {
                        $('#job-hour-details-row').hide();
                    }
                }
            });



            // Select / Deselect field checkbox when field is being filled / empty
            $('.activate_checkbox').keyup(function() {
                if ($(this).data('target-check')) {
                    $('#' + $(this).data('target-check')).prop('checked', ($(this).val() ? true : false));
                }
            });


            // Check the time start and end at the form submission
            $('#daily-report-form').submit(function(e) {
                e.preventDefault();

                const timeStart = moment('{{ date('Y-m-d') }} ' + $('#time_start').val());
                const timeEnd = moment('{{ date('Y-m-d') }} ' + $('#time_end').val());

                // Controllo formato ora di lavoro
                if (!timeStart.isValid()) {
                    alert('Il formato dell\'orario di inizio lavoro deve essere specificato in hh:mm esempio: 08:15');
                    return false;
                }

                if (!timeEnd.isValid()) {
                    alert('Il formato dell\'orario di fine lavoro deve essere specificato in hh:mm esempio: 18:45');
                    return false;
                }

                // Controllare conferma per passaggio da giorno all'altro
                if (!timeEnd.isAfter(timeStart)) {
                    if (!confirm('Hai selezionato di aver lavorato dalle ore '+timeStart.format('HH:mm')+
                        ' del ' + timeStart.format('DD/MM/yyyy')+ ' alle ore '+ timeEnd.format('HH:mm')+' del giorno successivo, vuoi continuare?')) {
                        return false;
                    }

                    timeEnd.add(1, 'd');
                }

                // Controllare che il totale ore lavoro corrisponda al totale ore inserito nella descrizione lavoro
                var partialHours = 0;

                $('.job-split-hours').each(function () {
                    if ($(this).val()) {
                        partialHours = partialHours + parseFloat($(this).val());
                    }
                });

                var workedHours = (timeEnd.diff(timeStart, 'seconds') / 3600) - ($('#total_break_time').val() ? $('#total_break_time').val() : 0);

                if (partialHours != workedHours) {
                    alert('Il totale delle ore lavorate: (' + workedHours + ') deve essere uguale al totale ore inserite al punto 2 (' + partialHours + ').');
                    return false;
                }

                // Format the date for the database record
                $('#date_time_start').val(timeStart.format('yyyy-MM-DD HH:mm:00').toString());
                $('#date_time_end').val(timeEnd.format('yyyy-MM-DD HH:mm:00').toString());

                $('#daily-report-form').unbind('submit');
                $('#daily-report-form').submit();
            });

        });


        /**
         * Function used to add an input element + div for jobs that require hour specs
         * @param element
         */
        function addWorkHourDiv(element, detailAttr) {
            let randomId = 'job-details-' + Math.random().toString(36).substring(7);
            let mainClass = 'jb-det-' + detailAttr; // Nome senza spazi
            let fieldName = element.attr('name') + '_details';
                
            // Se il campo esiste già, non lo ricrea
            if ($('.' + mainClass).length > 0) {
                return;
            }
        
            // Creazione dinamica del div per le ore lavorate
            let row = `
                <div class="col-lg-4 col-md-12 ${mainClass}">
                    <label for="${randomId}" class="${mainClass}">
                        * Inserisci le ore lavorate per ${detailAttr.replace(/-/g, ' ')} <i class="fa fa-info-circle"></i>
                    </label>
                    <input type="number" min="0.25" class="form-control mb-15 job-split-hours replacecomma ${mainClass}" 
                           id="${randomId}" name="${fieldName}" step="0.25" 
                           placeholder="Inserisci le ore lavorate per ${detailAttr.replace(/-/g, ' ')}" required />
                </div>`;
            
            $('#job-hour-details-row').append(row).show();
        }



    </script>
@endsection
