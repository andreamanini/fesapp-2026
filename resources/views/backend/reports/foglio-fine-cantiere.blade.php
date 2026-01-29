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
                    <form id="foglio-fine-cantiere-form" method="POST"
                          @if(isset($customerReport->id))
                          action="{{ route('update_cst_report', $customerReport->id) }}"
                          @else
                          action="{{ route('store_cst_report') }}"
                          @endif>

                        @csrf

                        @if (isset($customerReport->id)){{ method_field('PATCH') }}@endif

                        <input type="hidden" id="png_signature" name="png_signature" value="" />
                        <input type="hidden" id="building_site_id" name="building_site_id" value="{{ $buildingSite->id }}" />
                        <input type="hidden" id="location_lat" name="location_lat" value="{{ old('location_lat') }}" />
                        <input type="hidden" id="location_lng" name="location_lng" value="{{ old('location_lng') }}" />

                        @isset($reportsIds)
                            @foreach($reportsIds as $report)
                                <input type="hidden" name="report_id[]" value="{{ $report }}" />
                            @endforeach
                        @endisset


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
                            <div class="col-8">
                                <h5>Cantiere: {{ $buildingSite->site_name }}</h5>
                            </div>
                            <div class="col-4 text-right">
                                <h5>@isset($customerReport){{ $customerReport->transformDateField('created_at', 'd-m-Y') }}@else{{ date('d-m-Y') }}@endisset</h5>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6 fc-mb-input">
                                <div class="col-12">
                                    <h6>LAVORO ESEGUITO PRESSO:</h6>
                                </div>
                                <div class="col-12">
                                    <div class="" style="display: inline-block; width: 100%;">
                                        <input type="text" class="form-control" id="company_name" name="company_name"
                                               value="@if(null !== old('company_name')){{ old('company_name') }}@else{{ $buildingSite->site_name }}@endif"
                                               placeholder="Nome" required />
                                    </div>
                                </div>
                                @php
                                    if (null !== old('company_address')) {
                                        $reportAddress = old('company_address');
                                        $reportCity = old('company_city');
                                    } else if (isset($customerReport)) {
                                        $reportAddress = $customerReport->company_address;
                                        $reportCity = $customerReport->company_city;
                                    } else if ($buildingSite->address and isset($buildingSite->address->route)) {
                                        $reportAddress = $buildingSite->address->route . ' ' . (isset($buildingSite->address->street_number) ? $buildingSite->address->street_number : '');
                                        $reportCity = (isset($buildingSite->address->locality) ? $buildingSite->address->locality : '');
                                    } else {
                                        $reportAddress = $buildingSite->customer->address;
                                        $reportCity = $buildingSite->customer->city;
                                    }
                                @endphp
                                <div class="col-12">
                                    <div class="" style="display: inline-block; width: 100%;">
                                        <input type="text" class="form-control" id="company_address" name="company_address"
                                               value="{{ $reportAddress }}"
                                               placeholder="Via" required />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="" style="display: inline-block; width: 100%;">
                                        <input type="text" class="form-control" id="company_city" name="company_city"
                                               value="{{ $reportCity }}"
                                               placeholder="Paese" required />
                                    </div>
                                </div>

                            </div>
                            <div class="col-6 fc-mb-input">
                                <div class="col-12">
                                    <h6>LAVORO DA FATTURARE A:</h6>
                                </div>
                                <div class="col-12">
                                    <label class="css-control css-control-warning css-radio">
                                        <input type="radio" class="css-control-input" name="billing_to" value="cliente"
                                        @if(('cliente' == old('billing_to') or null == old('billing_to')) or (isset($customerReport) and 'cliente' == $customerReport->billing_to)){{ 'checked' }}@endif
                                        />
                                        <span class="css-control-indicator fes-custom-radio-sq"></span> Cliente dove è stato eseguito il lavoro
                                    </label>

                                </div>
                                <div class="col-12">
                                    <label class="css-control css-control-warning css-radio">
                                        <input type="radio" class="css-control-input" name="billing_to" value="azienda terza"
                                        @if('azienda terza' == old('billing_to') or (isset($buildingSite) and '' != $buildingSite->customer->company_name)){{ 'checked' }}@endif />
                                        <span class="css-control-indicator fes-custom-radio-sq"></span>
                                    </label>
                                    <div class="" style="display: inline-block; width: 90%;">
                                        <input type="text" class="form-control" id="billing_to_company" name="billing_to_company"
                                               value="@if(null !== old('billing_to_company')){{ old('billing_to_company') }}@elseif(isset($buildingSite)){{ $buildingSite->customer->company_name }}@endif"
                                               placeholder="Nome Impresa" />
                                    </div>
                                </div>
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
                                           @if('a corpo' == old('job_type') or (isset($customerReport) and 'a corpo' == $customerReport->job_type)){{ 'checked' }}@endif required />
                                    <span class="css-control-indicator fes-custom-radio-sq"></span> A CORPO
                                </label>
                            </div>
                            <div class="col-3 fes-col-dark">
                                <label class="css-control css-control-warning css-radio">
                                    <input type="radio" class="css-control-input" name="job_type" value="a consuntivo"
                                           @if('a consuntivo' == old('job_type') or (isset($customerReport) and 'a consuntivo' == $customerReport->job_type)){{ 'checked' }}@endif required />
                                    <span class="css-control-indicator fes-custom-radio-sq"></span> A CONSUNTIVO
                                </label>
                            </div>
                            <div class="col-2 fes-col-dark">
                                <label class="css-control css-control-warning css-radio">
                                    <input type="radio" class="css-control-input" name="job_type" value="ad euro/mq"
                                           @if('ad euro/mq' == old('job_type') or (isset($customerReport) and 'ad euro/mq' == $customerReport->job_type)){{ 'checked' }}@endif required />
                                    <span class="css-control-indicator fes-custom-radio-sq"></span> AD €/mq
                                </label>
                            </div>

                            <div class="col-12 mb-4">
                                <div class="form-group">
                                    <textarea class="form-control" id="work_description"
                                              name="work_description" rows="3" required>@if(null !== old('work_description')){{ old('work_description') }}@elseif(isset($customerReport)){{ $customerReport->work_description }}@elseif(isset($buildingSite->customer_notes)){{ $buildingSite->customer_notes }}@endif</textarea>
                                </div>
                            </div>
                            
                            @if(!isset($customerReport) or (isset($customerReport) and auth()->user()->isSuperAdmin()))

                                @if(isset($customerReport) and auth()->user()->isSuperAdmin())
                                    @php
                                        $reportMqArray = $customerReport->rows()->get();
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
                                            'struttS' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_sabbiata : null),
                                            'struttLavaggio' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_lavaggio : null),
                                            'struttSoffiatura' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_soffiatura : null),
                                            'struttIntonaco' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_intonaco : null),
                                            
                                            'struttverniciataAnticorrosiva' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_verniciata_anticorrosiva : null),
                                            'struttVerniciataCarrozzeria' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_verniciata_carrozzeria : null),
                                            'struttVerniciataImpregnante' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_verniciata_impregnante : null),
                                            'struttVerniciataIntumescente' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_verniciata_intumescente : null),
                                            'struttIntonaciIntumescenti' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_intonaci_intumescenti : null),
                                            'struttAltro' => (isset($reportMqArray[0]) ? $reportMqArray[0]->strutt_altro : null),
                                            
                                            'material' => (isset($reportMqArray[0]) ? $reportMqArray[0]->materiale : null),
                                            'qty' => (isset($reportMqArray[0]) ? $reportMqArray[0]->qty : null),
                                            'mqX' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_x : null),
                                            'mqY' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_y : null),
                                            'mqZ' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_z : null),
                                            'mqTot' => (isset($reportMqArray[0]) ? $reportMqArray[0]->mq_lavorati_tot : null),
                                            'workType' => (isset($reportMqArray[0]) ? $reportMqArray[0]->work_type : null),
                                            'idRow' => (isset($reportMqArray[0]->id) ? $reportMqArray[0]->id : 0)
                                        ])@endcomponent

                                        @if(isset($reportMqArray) and $reportMqArray->count() > 1)
                                            @for($i=1; $i<$reportMqArray->count(); $i++)
                                                @component('backend.components.daily-report-mq-row',[
                                                    'struttV' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_verniciata : null),
                                                    'struttS' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_sabbiata : null),
                                                    'struttLavaggio' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_lavaggio : null),
                                                    'struttSoffiatura' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_soffiatura : null),
                                                    'struttIntonaco' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_intonaco : null),
                                                    
                                                    'struttverniciataAnticorrosiva' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_verniciata_anticorrosiva : null),
                                                    'struttVerniciataCarrozzeria' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_verniciata_carrozzeria : null),
                                                    'struttVerniciataImpregnante' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_verniciata_impregnante : null),
                                                    'struttVerniciataIntumescente' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_verniciata_intumescente : null),
                                                    'struttIntonaciIntumescenti' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_intonaci_intumescenti : null),
                                                    'struttAltro' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->strutt_altro : null),
                                                    
                                                    'material' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->materiale : null),
                                                    'qty' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->qty : null),
                                                    'mqX' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_x : null),
                                                    'mqY' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_y : null),
                                                    'mqZ' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_z : null),
                                                    'mqTot' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->mq_lavorati_tot : null),
                                                    'workType' => (isset($reportMqArray[$i]) ? $reportMqArray[$i]->work_type : null),
                                                    'showRemoveBtn' => true,
                                                    'idRow' => $reportMqArray[$i]->id
                                                ])@endcomponent
                                            @endfor
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
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
                                                    $mqRow = ($row->mq_lavorati_tot * (!empty($row->qty) ? $row->qty : 1));
                                                    $totPartial += $mqRow;
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
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <h6>DICHIARAZIONE</h6>
                            </div>
                            <div class="col-3 mb-4">
                                <label for="register4-firstname">IL SOTTOSCRITTO</label>
                                <input type="text" class="form-control" id="signature_name"
                                       value="@if(null !== old('signature_name')){{ old('signature_name') }}@elseif(isset($customerReport)){{ $customerReport->signature_name }}@endif"
                                       name="signature_name" placeholder="Nome Completo" required />
                            </div>

                            <div class="col-4">
                                <label for="register4-firstname">DELLA SOCIETA'</label>
                                <input type="text" class="form-control" id="signature_company_name"
                                       value="@if(null !== old('signature_company_name')){{ old('signature_company_name') }}@elseif(isset($customerReport)){{ $customerReport->signature_company_name }}@else{{ $buildingSite->customer->company_name }}@endif"
                                       name="signature_company_name" placeholder="Nome Società" required />
                            </div>

                            <div class="col-5">
                                <label for="register4-firstname">DICHIARO CHE IL VOSTRO TECNICO</label>
                                <input type="text" class="form-control" id="employee_name"
                                       value="@if(null !== old('employee_name')){{ old('employee_name') }}@elseif(isset($customerReport)){{ $customerReport->employee_name }}@else{{ auth()->user()->name . ' '. auth()->user()->surname }}@endif"
                                       name="employee_name" placeholder="Nome Tecnico" required />
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
                                    <label style="margin-top: 10px;" for="wizard-progress-bio">NOTE:</label>
                                    <textarea class="form-control" id="additional_notes"
                                              name="additional_notes" rows="2">@if(null !== old('additional_notes')){{ old('additional_notes') }}@elseif(isset($customerReport)){{ $customerReport->additional_notes }}@endif</textarea>
                                </div>
                            </div>

                            <div class="col-12 signature-wrapper">
                                <label style="margin-top: 10px;" for="wizard-progress-bio">FIRMA DEL CLIENTE PER ACCETTAZIONE:</label><br />
                                @if(!isset($customerReport))
                                    <canvas id="signature-pad" class="signature-pad" width="650" height="200" style="border:1px solid black"></canvas><br />

                                    <button type="button" id="erase-signature" class="btn btn-secondary fes-btn-w">
                                        <i class="fa fa-eraser mr-5"></i> Cancella firma
                                    </button>
                                @else
                                    <img src="{{ asset($customerReport->customer_signature) }}" width="300px"/>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
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
    <script src="{{ asset('backend/js/plugins/signature-pad/signature-pad.min.js') }}"></script>
    <script src="{{ asset('backend/js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('backend/js/handlebars.min.js') }}"></script>
    <script>jQuery(function(){ Codebase.helpers(['ckeditor']); });</script>
    <script src="{{ asset('backend/js/daily-report.js') }}"></script>
    <script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
    <script>
        // richiedi posizione gps
        getLocation();

        $(document).ready(function() {
            
            // inizio autocomplete
            addAutoComplete();

            CKEDITOR.replace('work_description', {
                language: 'it'
                //removePlugins: [ 'uploadimage', 'uploadwidget', 'widget', 'tableselection' ]
            });

            $("td[data-title=AGGIUNGI]").eq(0).click(function(){
                setTimeout(addAutoComplete, 1000);
            });


            var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)',
                onEnd: function() {
                    $('#png_signature').val(signaturePad.toDataURL('image/png'));
                }
            });
            // var saveButton = document.getElementById('save-signature');
            var cancelButton = document.getElementById('erase-signature');

            cancelButton.addEventListener('click', function (event) {
                signaturePad.clear();
                $('#png_signature').val('');
            });


            // Add custom square meters button function
            $('#add-fc-mq').click(function() {
                $(this).hide();

                $('#custom-fc-mq').show();
            });



            // On form submit
            $('#foglio-fine-cantiere-form').submit(function(e){
                e.preventDefault();

                // Check for work type field completion
                if (checkWorkTypeSelectionHasErrors()) {
                    alert('Seleziona una tipologia di lavorazione per ogni MQ inserito!');
                    return false;
                }

                $('#foglio-fine-cantiere-form').unbind('submit');
                $('#foglio-fine-cantiere-form').submit();
            });
        });
        
        function addAutoComplete() {
            $("input[name^=struttura]").autocomplete({
                source: function( request, response ) {
                  $.ajax( {
                    type: "POST",  
                    url: "{{ route('autocomplete') }}",
                    data: { term: request.term, _token: '{{csrf_token()}}', type: 'struttura' },
                    dataType: "jsonp",
                    success: function( data ) {
                      response( data );
                    }
                  } );
                },
                minLength: 2
            });
            
            $("input[name^=materiale]").autocomplete({
                source: function( request, response ) {
                  $.ajax( {
                    type: "POST",  
                    url: "{{ route('autocomplete') }}",
                    data: { term: request.term, _token: '{{csrf_token()}}', type: 'materiale' },
                    dataType: "jsonp",
                    success: function( data ) {
                      response( data );
                    }
                  } );
                },
                minLength: 2
            });
        }
        
    </script>
@endsection
