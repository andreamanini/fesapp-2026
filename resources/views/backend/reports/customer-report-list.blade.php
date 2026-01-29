@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Lista fogli fine cantiere</h3>
                </div>
                <div class="block-content">
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

                    <div class="row">
                        <div class="col-md-12">
                        <a id="index" class="navbar-brand float-right" href="{{route('all_cst_report_pdf')}}?date_from={{ $startDate ?? '' }}&date_to={{ $endDate ?? '' }}&building_site_id=@if(isset($_GET['building_site_id'])){{$_GET['building_site_id']}}@endif&eid=@if(isset($_GET['eid'])){{$_GET['eid']}}@endif">
                              <button type="button" class="btn btn-success">Esporta tutto</button>
                        </a>
                        </div>
                    </div>
                    
                    <br>

                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th class="text-center"></th>
                            <th>Dipendente</th>
                            <th class="d-none d-sm-table-cell">Cantiere</th>
                            <th class="d-none d-sm-table-cell">Data</th>
                            <th class="text-center" style="width: 100px;">Dettagli</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($reports->count() > 0)
                            @php
                                $datecolor = true;
                                $actualday = '';
                            @endphp
                            @foreach($reports as $report)
                                @php
                                    $nextday = date('Y-m-d',strtotime($report->created_at));
                                    if ($nextday != $actualday) $datecolor = !$datecolor;
                                    ($datecolor) ? $rowcolor = 'class=table-warning' : $rowcolor = 'class=table-light';
                                    $actualday = $nextday;
                                @endphp
                                @component('backend.components.customer-report-table-row', [
                                    'report' => $report,
                                    'loop' => $loop,
                                    'rowcolor' => $rowcolor
                                ])@endcomponent
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">
                                    Non sono presenti fogli fine cantiere da mostrare associati al mese di ricerca.
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('footer')

    @component('backend.components.delete-modal', [
        'recordName' => 'report di fine cantiere'
    ])@endcomponent

    <script>
        $(document).ready(function() {
            var dateFormat = "dd-mm-yy",
                from = $("#df")
                    .datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        numberOfMonths: 2,
                        dateFormat: 'dd-mm-yy'
                    })
                    .on("change", function() {
                        to.datepicker( "option", "minDate", getDate( this ) );
                        location.href = '{{ route('customer_report_list') }}?date_from=' + $("#df").val() +
                            '&date_to=' + $('#dt').val() +
                            ($('#building_site_id option:selected').val() ? '&building_site_id=' + $('#building_site_id option:selected').val() : '') +
                            ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');

                    }),
                to = $("#dt").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 2,
                    dateFormat: 'dd-mm-yy'
                })
                    .on("change", function() {
                        from.datepicker( "option", "maxDate", getDate( this ) );
                        location.href = '{{ route('customer_report_list') }}?date_from=' + $("#df").val() +
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

            // Employee select change
            $('#eid').change(function () {
                location.href = '{{ route('customer_report_list') }}?date_from=' + $("#df").val() +
                    '&date_to=' + $('#dt').val() +
                    ($('#building_site_id option:selected').val() ? '&building_site_id=' + $('#building_site_id option:selected').val() : '') +
                    ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');
            });

            // Building site select change
            $('#building_site_id').change(function () {
                location.href = '{{ route('customer_report_list') }}?date_from=' + $("#df").val() +
                    '&date_to=' + $('#dt').val() +
                    ($('#building_site_id option:selected').val() ? '&building_site_id=' + $('#building_site_id option:selected').val() : '') +
                    ($('#eid option:selected').val() ? '&eid=' + $('#eid option:selected').val() : '');
            });
        });
    </script>
@endsection