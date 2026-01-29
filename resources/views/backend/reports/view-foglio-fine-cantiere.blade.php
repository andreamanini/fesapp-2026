@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Foglio fine cantiere</h3>
                </div>
                <div class="block-content">

                    <div class="form-group row">
                        <div class="col-8">
                            <h5>Cantiere: {{ $buildingSite->site_name }}</h5>
                        </div>
                        <div class="col-4 text-right">
                            <h5>{{ $customerReport->created_at->format('d-m-Y') }}</h5>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 fc-mb-input">
                            <div class="col-12">
                                <h6>LAVORO ESEGUITO PRESSO:</h6>
                            </div>
                            <div class="col-12">
                                <div class="" style="display: inline-block; width: 100%;">
                                    {{ $customerReport->company_name }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="" style="display: inline-block; width: 100%;">
                                    {{ $customerReport->company_address }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="" style="display: inline-block; width: 100%;">
                                    {{ $customerReport->company_city }}
                                </div>
                            </div>

                        </div>
                        <div class="col-6 fc-mb-input">
                            <div class="col-12">
                                <h6>LAVORO DA FATTURARE A:</h6>
                            </div>

                            @if('cliente' == $customerReport->billing_to)
                            <div class="col-12">
                                <label class="css-control css-control-warning css-radio">
                                    <input type="radio" class="css-control-input" name="billing_to" value="cliente" checked />
                                    <span class="css-control-indicator fes-custom-radio-sq"></span> Cliente dove è stato eseguito il lavoro
                                </label>

                            </div>
                            @endif

                            @if('azienda terza' == $customerReport->billing_to)
                            <div class="col-12">
                                <label class="css-control css-control-warning css-radio">
                                    <input type="radio" class="css-control-input" name="billing_to" value="azienda terza" checked />
                                    <span class="css-control-indicator fes-custom-radio-sq"></span>
                                </label>
                                <div class="" style="display: inline-block; width: 90%;">
                                    {{ $customerReport->billing_to_company }}
                                </div>
                            </div>
                            @endif

                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-5 fes-col-dark">
                            <h6>DESCRIZIONE LAVORO</h6>
                        </div>

                        <div class="col-2 fes-col-dark">
                            <label class="css-control css-control-warning css-radio">
                                <input type="radio" class="css-control-input" name="job_type" value="a corpo"
                                @if('a corpo' == $customerReport->job_type){{ 'checked' }}@endif />
                                <span class="css-control-indicator fes-custom-radio-sq"></span> A CORPO
                            </label>
                        </div>
                        <div class="col-3 fes-col-dark">
                            <label class="css-control css-control-warning css-radio">
                                <input type="radio" class="css-control-input" name="job_type" value="a consuntivo"
                                @if('a consuntivo' == $customerReport->job_type){{ 'checked' }}@endif />
                                <span class="css-control-indicator fes-custom-radio-sq"></span> A CONSUNTIVO
                            </label>
                        </div>
                        <div class="col-2 fes-col-dark">
                            <label class="css-control css-control-warning css-radio">
                                <input type="radio" class="css-control-input" name="job_type" value="ad euro/mq"
                                @if('ad euro/mq' == $customerReport->job_type){{ 'checked' }}@endif />
                                <span class="css-control-indicator fes-custom-radio-sq"></span> AD €/mq
                            </label>
                        </div>

                        <div class="col-12 mb-4">
                            <div class="form-group">
                                {!! $customerReport->work_description !!}
                            </div>
                        </div>
                        
                        @php
                            $reportRows = $customerReport->rows()->get();
                            $totPartial = 0;
                         
                        @endphp
                        <div class="col-12 mb-4">
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
                                            #$mqRow = ($row->mq_lavorati_tot * (!empty($row->qty) ? $row->qty : 1));
                                            $totPartial += $row->mq_lavorati_tot;
                                        @endphp
                                        <tr>
                                            <td data-title="TIPO LAVORO">
                                                {{ $customerReport->getWorkTypeName($row->work_type) }}
                                            </td>
                                            <td data-title="STRUTTURA ">
                                                {{ $row->{$customerReport->getWorkTypeFieldName($row->work_type)} }}
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
                                                {{ $row->mq_lavorati_tot }} mq
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
                        </div>
                        @php /*
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
                    </div>
                    */
                    @endphp
                    <hr>

                    <div class="form-group row">
                        <div class="col-12 text-center">
                            <h6>DICHIARAZIONE</h6>
                        </div>
                        <div class="col-3 mb-4">
                            <label for="register4-firstname">IL SOTTOSCRITTO</label><br />
                            {{ $customerReport->signature_name }}
                        </div>

                        <div class="col-4">
                            <label for="register4-firstname">DELLA SOCIETA'</label><br />
                            {{ $customerReport->signature_company_name }}
                        </div>

                        <div class="col-5">
                            <label for="register4-firstname">DICHIARO CHE IL VOSTRO TECNICO</label><br />
                            {{ $customerReport->employee_name }}
                        </div>

                        <div class="col-12">
                            <p>HA ESEGUITO I LAVORI LASCIANDO IN PERFETTE CONDIZIONI IL LUOGO DI LAVORO E
                                DI AVER VERIFICATO LA CORRETTEZZA DEI DATI SCRITTI NELLA PRESENTE COMMESSA
                                <strong class="font-weight-bold">(COMPRESO METRATURE E/O ORE) E CHE LE LAVORAZIONI SONO STATE ESEGUITE
                                    A REGOLA D'ARTE SENZA NULLA DA CONTESTARE.</strong><br>
                                (l'eventuale isolamento del luogo di lavoro, salvo diverso accordo scritto
                                è a carico del Cliente)
                            </p>
                        </div>

                        <div class="col-12" align="center">
                            <hr />
                            <p style="font-size:9px">{!! __('fes.customer_report_legal') !!}</p>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label style="margin-top: 10px;" for="wizard-progress-bio">NOTE:</label><br />
                                @isset($customerReport->additional_notes){{ $customerReport->additional_notes }}@else{{ 'Nessuna nota aggiuntiva.' }}@endisset
                            </div>
                        </div>

                        <div class="col-12 signature-wrapper">
                            <label style="margin-top: 10px;" for="wizard-progress-bio">FIRMA DEL CLIENTE PER ACCETTAZIONE:</label><br />
                            <img src="{{ asset($customerReport->customer_signature) }}" width="300px"/>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-12 btn-right">
                            <a href="{{ route('cst_report_pdf', $customerReport->id) }}" class="btn btn-alt-primary">
                                <i class="fa fa-file-pdf-o mr-5"></i> Scarica PDF
                            </a>

                            <a href="{{ route('building-sites.show', $buildingSite->id) }}" class="btn btn-alt-secondary">
                                <i class="fa fa-remove mr-5"></i> Annulla
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
