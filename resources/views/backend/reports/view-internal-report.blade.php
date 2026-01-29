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

                        <div class="form-group row">
                            <div class="col-4">
                                <h5>Operaio: {{ $report->employee->name }} {{ $report->employee->surname }}</h5>
                            </div>
                            <div class="col-4">
                            </div>
                            <div class="col-4 text-right">
                                <h5>Inviato il: {{ $report->created_at }}</h5>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <h5>
                                    Capannone / Deposito FES - Lonato del Garda
                                </h5>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3">
                                <label for="truck_no">N. Camion</label><br />
                                {{ $report->truck_no }}
                            </div>
                            <div class="col-3">
                                <label for="break_time">Nome del guidatore</label><br />
                                {{ $report->truck_driver_name }}
                            </div>
                            <div class="col-3">
                                <label for="total_break_time">Orario di inizio lavoro</label><br />
                                {{ $timeStart->format('d-m-Y H:i') }}
                            </div>
                            <div class="col-3">
                                <label for="total_break_time">Orario di fine lavoro</label><br />
                                {{ $timeEnd->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        <div class="form-group row pt-20">
                            <div class="col-3">
                                <label for="travel_time">Totale ore viaggio</label><br />
                                {{ $report->travel_time ?? 0 }}

                            </div>
                            <div class="col-3">
                                <label for="meals_no">N. pasti e totale speso</label><br />
                                {{ $report->meals_no ?? 0 }}
                            </div>
                            <div class="col-3">
                                <label for="break_time">Totale ore pausa</label><br />
                                {{ $report->total_break_time ?? 0 }}
                            </div>
                            <div class="col-3">
                                <label for="break_from_to">Orario di pausa</label><br />
                                {{ $report->break_from_to ?? 'non specificato' }}
                            </div>
                        </div>

                        <div class="form-group row pt-20">
                            <div class="col-3">
                                <label for="travel_time">Totale ore lavorate</label><br />
                                {{ $report->total_working_hours ?? 0 }}

                            </div>
                        </div>


                        <hr>
                        <div class="form-group row pt-20">
                            <div class="col-12">
                                <h6  class="h-number-compil">1</h6><h6 class="h-compil">OPERAI AL CAPANNONE</h6>
                            </div>
                            <div class="col-12 row align-items-center">
                                <div class="col-8" id="employees-container">
                                    @if(null !== $report->employees)
                                        @foreach(json_decode($report->employees) as $employee)
                                            <span class="comp-operaio mr-5">
                                                {{ $employee }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>


                        </div>

                        <hr>
                        @php
                            $job = json_decode($report->job_details);
                        @endphp
                        <div class="form-group row row-descrizione-lavoro pt-20">
                            <div class="col-12">
                                <h6  class="h-number-compil">2</h6><h6 class="h-compil">DESCRIZIONE LAVORO</h6>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Coperture -- Ore: {{ $job->job_coperture ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Stuccature -- Ore: {{ $job->job_stuccature ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Carteggiature -- Ore: {{ $job->job_carteggiatura ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Lavaggio -- Ore: {{ $job->job_lavaggio ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Sabbiatura -- Ore: {{ $job->job_sabbiatura ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Verniciatura -- Ore: {{ $job->job_verniciatura ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Discarica -- Ore: {{ $job->job_discarica ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Lavaggio camion ed attrezzatura -- Ore: {{ $job->job_lavaggio_camion ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Ritiro materiale -- Ore: {{ $job->job_ritiro_materiale ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Ordine dentro capannone -- Ore: {{ $job->job_ordine_dentro_capannone ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Ordine fuori capannone -- Ore: {{ $job->job_ordine_fuori_capannone ?? '0' }}</p>
                            </div>

                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Pulizia -- Ore: {{ $job->job_pulizia ?? '0' }}</p>
                            </div>
                            
                            <div class="col-6 col-sm-4 col-md-3">
                                <p>Altro -- Ore: {{ $job->job_other ?? '0' }}</p>
								@isset($job->job_other_text){{ $job->job_other_text }}@endisset
                            </div>

                        </div>

                        <div class="form-group row row-descrizione-lavoro">

                            <div class="col-12">
                                <div class="form-group">
                                    <label style="margin-top: 10px;" for="work_description">Descrizione lavori eseguiti</label><br />
                                    {{ $report->work_description ?? 'Nessuna descrizione inserita.' }}<br /><br />
                                </div>
                            </div>

                        </div>

                        <hr>

                        @php
                            $equipment = json_decode($report->equipment);
                        @endphp
                        <div class="form-group row row-attrezzatura pt-20">
                            <div class="col-12">
                                <h6  class="h-number-compil">3</h6><h6 class="h-compil">ATTREZZATURA</h6>
                            </div>

                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_idropulitrici">Idropulitrici N°</label><br />
                                    {{ $equipment->equipment_idropulitrici ?? '---' }}
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_intonacatrici">Intonacatrici N°</label><br />
                                    {{ $equipment->equipment_intonacatrici ?? '---' }}
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_airmix">Airmix N°</label><br />
                                    {{ $equipment->equipment_airmix ?? '---' }}
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_airless">Airless N°</label><br />
                                    {{ $equipment->equipment_airless ?? '---' }}
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_ponteggi">Ponteggi Colore</label><br />
                                    {{ $equipment->equipment_ponteggi ?? '---' }}
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_scale_colore">Scale Colore</label><br />
                                    {{ $equipment->equipment_scale_colore ?? '---' }}
                                </div>
                            </div>

                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_carteggiatrici">Carteggiatrici</label><br />
                                    {{ $equipment->equipment_carteggiatrici ?? '---' }}
                                </div>
                            </div>
                            
                            <div class="col-6 col-sm-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_altro">Altro</label><br />
                                    {{ $equipment->equipment_altro ?? '---' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="equipment_strumentazione">Strumentazione</label><br />
                                    {{ $equipment->equipment_strumentazione ?? '---' }}
                                </div>
                            </div>

                        </div>

                        <hr>

                        @php
                            $materials = json_decode($report->materials);
                        @endphp
                        <div class="form-group row row-materiali pt-20">
                            <div class="col-12">
                                <h6  class="h-number-compil">4</h6><h6 class="h-compil">MATERIALI</h6>
                            </div>
                            <div class="col-6 col-lg-4 ">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_gasolio_camion">Gasolio camion Lt</label><br />
                                    {{ $materials->materials_gasolio_camion ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 ">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_gasolio_compressore">Gasolio compressore Lt</label><br />
                                    {{ $materials->materials_gasolio_compressore ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 ">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_gasolio_altro">Gasolio altro Lt</label><br />
                                    {{ $materials->materials_gasolio_altro ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 ">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_diluente">Diluente Lt</label><br />
                                    {{ $materials->materials_diluente ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 ">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_intonacatrici">Intonacatrici N</label><br />
                                    {{ $materials->materials_intonacatrici ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6 col-lg-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_big_bag">Big bag N</label><br />
                                    {{ $materials->materials_big_bag ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6 col-lg-4">
                                <div style="display: inline-block; width: 85%;">
                                    <label for="materials_km_giornalieri">Km Giornalieri</label><br />
                                    {{ $materials->materials_km_giornalieri ?? '---' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div style="display: inline-block; width: 15%;">
                                    <label for="materials_sacchi">Sacchi N</label><br />
                                    {{ $materials->materials_sacchi ?? '---' }}
                                </div>
                                <div class="inp-descr-comp">
                                    <label for="materials_sacchi_descr">Descrizione</label><br />
                                    {{ $materials->materials_sacchi_descr ?? '---' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div style="display: inline-block; width: 15%;">
                                    <label for="materials_latte">Latte N</label><br />
                                    {{ $materials->materials_latte ?? '---' }}
                                </div>
                                <div class="inp-descr-comp">
                                    <label for="materials_latte_descr">Descrizione</label><br />
                                    {{ $materials->materials_latte_descr ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="inp-altro-comp">
                                    <label for="materials_other">Altro</label><br />
                                    {{ $materials->materials_other ?? '---' }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="inp-altro-comp">
                                    <label for="extra_expenses">Spese Extra</label><br />
                                    {{ $report->extra_expenses ?? '---' }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="save-form-div">
                            <div class="col-12 btn-right">
                                <a href="javascript:history.back(1)" class="btn btn-alt-secondary">
                                    <i class="fa fa-remove mr-5"></i> Chiudi
                                </a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('backend//js/plugins/masked-inputs/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('backend/js/moment.js') }}"></script>
    <script>
        jQuery(function(){ Codebase.helpers(['masked-inputs']); });
    </script>
@endsection
