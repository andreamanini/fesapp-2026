@extends('layouts.fes-app')

@section('content')

    <!-- Notifica per nuovo programma di lavoro -->
    @if($showWorkNotification)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Nuovo programma di lavoro disponibile!</strong> È stato caricato un nuovo programma di lavoro per te.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif


    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Lista Cantieri</h3>
                </div>
                <div class="block-content">
                    <div class="block">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p>Stai visualizzando il periodo dal <strong>{{ $dateFrom->format('d-m-Y') }}</strong> al <strong>{{ $dateTo->format('d-m-Y') }}</strong></p>
                            </div>


                            <div class="col-6">
                                <label for="df">Data di inizio</label>
                                <input type="text" id="df" value="{{ $dateFrom->format('d-m-Y') }}" readonly style="max-width:100px;margin-right:10px" />

                                <label for="dt">Data di fine</label>
                                <input type="text" id="dt" value="{{ $dateTo->format('d-m-Y') }}" readonly style="max-width:100px" />
                            </div>
                        </div>
                        @if(!is_null($dateFrom) && Auth::user()->isAdmin())
                        <div class="row pb-15">
                            <div class="col-md-6">
                                <select name="eid" id="eid" class="form-control" style="max-width: 200px;">
                                    <option value="" @if(null == $employeeIdFilter){{ 'selected' }}@endif>Filtra per dipendente</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @if($employee->id == $employeeIdFilter){{ 'selected' }}@endif>{{ $employee->name }} {{ $employee->surname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="building_site_id" id="building_site_id" class="form-control" style="max-width: 200px;">
                                    <option value="" @if(null == $buildingSiteIdFilter){{ 'selected' }}@endif>Filtra per cantiere</option>
                                    @foreach($buildingSites as $buildingSite)
                                        <option value="{{ $buildingSite->id }}" @if($buildingSite->id == $buildingSiteIdFilter){{ 'selected' }}@endif>{{ $buildingSite->site_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        @if($showCompileReportWarning)
                            <div class="alert alert-danger alert-dismissable" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h3 class="alert-heading font-size-h4 font-w400">Attenzione</h3>
                                <p class="mb-0">È presente un rapportino di lavoro giornaliero incompleto che deve essere completato</p><br />
                                <a href="{{ route('reports.update', $firstIncompleteReport->id) }}" class="btn btn-danger">Clicca qui per procedere alla compilazione</a>
                                <a href="{{ route('reports.forceclose', $firstIncompleteReport->id) }}" class="btn btn-success">Clicca qui per segnalare il rapportino come completo</a>
                            </div>
                        @endif

                        <div class="block-content block-content-full">
                            @if(Auth::user()->isAdmin())
                            <label for="show_closed">
                                <input type="checkbox" id="show_closed" name="show_closed"
                                       @if('true' == request('show_closed')){{ 'checked' }}@endif
                                       value="true" />
                                Mostra cantieri / commesse chiuse
                            </label> <br /> <br />
                            @endif
                            <!-- DataTables functionality is initialized with .js-dataTable-full class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th>Cantiere</th>
                                    <th>Cliente</th>
                                    <th class="d-none d-sm-table-cell">Indirizzo</th>
                                    <th class="d-none d-sm-table-cell" style="width: 15%;">Tipologia</th>
                                    <th class="d-none d-sm-table-cell">Operai</th>
                                    <th class="text-center" style="width: 15%;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($buildingSites as $bs)
                                    @component('backend.components.building-site-table-row', [
                                        'bs' => $bs,
                                        'loop' => $loop
                                    ])@endcomponent
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')

    @component('backend.components.delete-modal', [
        'recordName' => 'cantiere'
    ])@endcomponent

    <script>
        $(document).ready(function() {
            $('#show_closed').change(function() {
                if ($(this).prop('checked')){
                    window.location.href = '{{ route('building-sites.index') }}?show_closed=true&date_from=' + $("#df").val()+'&date_to=' + $('#dt').val() +
                            ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');
                } else {
                    window.location.href = '{{ route('building-sites.index') }}?date_from=' + $("#df").val()+'&date_to=' + $('#dt').val() +
                            ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');                    
                }    
            });
            @if(!is_null($dateFrom))
            var dateFormat = "dd-mm-yy",
                from = $("#df")
                    .datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        numberOfMonths: 2,
                        dateFormat: 'dd-mm-yy',
                    })
                    .on("change", function() {
                        to.datepicker( "option", "minDate", getDate( this ) );
                        location.href = '{{ route('building-sites.index') }}?date_from=' + $("#df").val() +
                            '&date_to=' + $('#dt').val() +
                            ($('#building_site_id option:selected').val() ? '&building_site_id=' + $('#building_site_id option:selected').val() : '') +
                            ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');
                    }),
                to = $("#dt").datepicker({
                    minDate: '{{ $dateFrom->format('d-m-Y') }}',
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 2,
                    dateFormat: 'dd-mm-yy'
                })
                    .on("change", function() {
                        from.datepicker( "option", "maxDate", getDate( this ) );
                        location.href = '{{ route('building-sites.index') }}?date_from=' + $("#df").val() +
                            '&date_to=' + $('#dt').val() +
                            ($('#building_site_id option:selected').val() ? '&building_site_id=' + $('#building_site_id option:selected').val() : '') +
                            ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');
                    });

            // Employee select change
            $('#eid').change(function () {
                location.href = '{{ route('building-sites.index') }}?date_from=' + $("#df").val() +
                    '&date_to=' + $('#dt').val() +
                    ($('#building_site_id option:selected').val() ? '&building_site_id=' + $('#building_site_id option:selected').val() : '') +
                    ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');
            });
            
            // Building site select change
            $('#building_site_id').change(function () {
                location.href = '{{ route('building-sites.index') }}?date_from=' + $("#df").val() +
                    '&date_to=' + $('#dt').val() +
                    ($('#building_site_id option:selected').val() ? '&building_site_id=' + $('#building_site_id option:selected').val() : '') +
                    ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');
            });

            function getDate( element ) {
                var date;
                try {
                    date = $.datepicker.parseDate( dateFormat, element.value );
                } catch( error ) {
                    date = null;
                }

                return date;
            }
            @endif
        });
    </script>
@endsection