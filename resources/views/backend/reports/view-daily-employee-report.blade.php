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
                                Cantiere: {{ $report->buildingSite->site_name }}
                                @if(null !== $report->buildingSite->address and isset($report->buildingSite->address->autocomplete))
                                    - {{ $report->buildingSite->address->autocomplete }}
                                @endif
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
                            <h6  class="h-number-compil">1</h6><h6 class="h-compil">OPERAI IN CANTIERE</h6>
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
                            <p>Intonaco -- Ore: {{ $job->job_intonaco ?? '0' }}</p>
                        </div>

                        <div class="col-sm-8 col-md-9">
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
                        <!--
                        @php
                            $reportRows = $report->rows()->get();
                            $totPartial = 0;
                        @endphp
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
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($reportRows->count() == 0)
                                        <tr>
                                            <td colspan="7" data-title="Metri quadri">Nessun record da mostrare</td>
                                    @endif

                                    @foreach($reportRows as $row)
                                        @php
                                            $mqRow = ($row->mq_lavorati_tot * (!empty($row->qty) ? $row->qty : 1));
                                            $totPartial += $mqRow;
                                        @endphp
                                        <tr>
                                            <td data-title="TIPO LAVORO">
                                                {{ $report->getWorkTypeName($row->work_type) }}
                                            </td>
                                            <td data-title="STRUTTURA ">
                                                {{ $row->{$report->getWorkTypeFieldName($row->work_type)} }}
                                            </td>
                                            <td data-title="MATERIALE">
                                                {{ $row->materiale }}
                                            </td>
                                            <td data-title="QUANTITÀ">
                                                {{ $row->qty }}
                                            </td>
                                            <td data-title="MISURA (in metri)">
                                                {{ $row->mq_lavorati_x }}
                                                X
                                                {{ $row->mq_lavorati_y }}
                                                X
                                                {{ $row->mq_lavorati_z }}
                                            </td>
                                            <td data-title="TOTALE">
                                                {{ $mqRow }} mq
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if($totPartial > 0)
                                        <tr>
                                            <td colspan="4">&nbsp;</td>
                                            <td>Totale</td>
                                            <td>{{ $totPartial }} mq</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>-->
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

                        <div class="col-12">
                            <div style="display: inline-block; width: 85%;">
                                <label for="equipment_strumentazione">Strumentazione</label><br />
                                {{ $equipment->equipment_strumentazione ?? '---' }}
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="form-group row pt-20">
                        <div class="col-12">
                            <h6  class="h-number-compil">4</h6><h6 class="h-compil">LAVORI EXTRA</h6><br />
                            @if(null !== $report->extra_work_description)
                                <p style="color:blue"><strong>{{ $report->extra_work_description }}</strong></p>
                            @else
                                <p>Nessuna lavorazione extra da segnalare.</p>
                            @endif
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="time_lost">Tempo perso / Ore di fermo</label><br />
                                {{ $report->time_lost ?? '0' }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    @php
                        $materials = json_decode($report->materials);
                    @endphp
                    <div class="form-group row row-materiali pt-20">
                        <div class="col-12">
                            <h6  class="h-number-compil">5</h6><h6 class="h-compil">MATERIALI</h6>
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
                            <a href="{{ route('daily_report_pdf', $report->id) }}" class="btn btn-alt-primary">
                                <i class="fa fa-file-pdf-o mr-5"></i> Scarica PDF
                            </a>

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
