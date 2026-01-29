@extends('layouts.fes-app')

@section('header')
    <link rel="stylesheet" href="{{ asset('backend/css/year-timeline.css') }}" />
@endsection

@section('content')
    <div class="row mb-50">
        <div class="col-12">
            <div class="meter" id='meter'>
                <span class="year" style="width: calc(7.7% * {{ $month }});" id='span'>
                    <span class="progress" id ='progresso'></span>
                </span>

                <div class="months" id="mesi">
                    <div class="month">
                    </div>
                    @for($m=1; $m<=(session()->has('dashboard_year') ? 12 : date('n')); $m++)
                    <div class="month">
                        <a href="{{ route('dashboard') }}?month={{ ($m < 10 ? "0$m" : $m) }}">
                            <div class="bullet"></div>
                            <div class="m-name">
                                <p>
                                    @if($month == ($m < 10 ? "0$m" : $m))
                                    <strong style="color: black">{{ date("M", strtotime('2016-'.($m < 10 ? "0$m" : $m) . '-17 16:41:51')) }}</strong>
                                    @else
                                    {{ date("M", strtotime('2016-'.($m < 10 ? "0$m" : $m) . '-17 16:41:51')) }}
                                    @endif
                                    </p>
                            </div>
                        </a>
                    </div>
                    @endfor

                </div>
            </div>
        </div>
    </div>

    <!--            <h2 class="content-heading">Utenti app / dipendenti</h2>-->
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Page Content -->
            <div class="content">
                <div class="row invisible" data-toggle="appear">
                    <!-- Row #1 -->
                    <div class="col-6 col-xl-3">
                        <a class="block block-link-shadow text-right" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-left mt-10 d-none d-sm-block">
                                    <i class="fa fa-flask fa-3x text-body-bg-dark"></i>
                                </div>
                                <div class="font-size-h3 font-w600" data-toggle="countTo" data-speed="1000" data-to="{{ $dashboardStats->gasolineLt }}">0</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Lt gasolio utilizzati</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-xl-3">
                        <a class="block block-link-shadow text-right" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-left mt-10 d-none d-sm-block">
                                    <i class="fa fa-pencil-square fa-3x text-body-bg-dark"></i>
                                </div>
                                <div class="font-size-h3 font-w600"><span data-toggle="countTo" data-speed="1000" data-to="{{ $dashboardStats->totMq }}">0</span></div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Mq lavorati</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-xl-3">
                        <a class="block block-link-shadow text-right" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-left mt-10 d-none d-sm-block">
                                    <i class="fa fa-hourglass-3 fa-3x text-body-bg-dark"></i>
                                </div>
                                <div class="font-size-h3 font-w600" data-toggle="countTo" data-speed="1000" data-to="{{ $dashboardStats->totHours }}">0</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Ore lavorate</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-xl-3">
                        <a class="block block-link-shadow text-right" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-left mt-10 d-none d-sm-block">
                                    <i class="si si-user fa-3x text-body-bg-dark"></i>
                                </div>
                                <div class="font-size-h3 font-w600">Utente</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">{{ auth()->user()->name }} {{ auth()->user()->surname }}</div>
                            </div>
                        </a>
                    </div>
                    <!-- END Row #1 -->
                </div>
                <div class="row invisible" data-toggle="appear">
                    <!-- Row #3 -->
                    <div class="col-md-4">
                        <div class="block">
                            <div class="block-content block-content-full" style="min-height: 350px">
                                <div class="py-20 text-center">
                                    <div class="mb-20">
                                        <i class="fa fa-envelope-open fa-4x text-primary"></i>
                                    </div>
                                    <div class="font-size-h4 font-w600">{{ $dashboardStats->monthlyReports }} Rapportini</div>
                                    <div class="text-muted">Verifica i rapportini giornalieri inviati</div>
                                    <div class="pt-20">
                                        @php
                                            $lastDay = date("t-m-$year", strtotime(date("01-{$month}-$year")));
                                        @endphp
                                        <a class="btn btn-rounded btn-alt-info" href="{{ route('report_list') }}?date_from={{ date("01-{$month}-$year") }}&date_to={{ $lastDay }}">
                                            <i class="fa fa-cog mr-5"></i> Controlla rapportini
                                        </a>
                                        <br><br>
                                        <a class="btn btn-rounded btn-alt-info" href="{{ route('not_compiled_report') }}?date_from={{ date("01-{$month}-$year") }}&date_to={{ $lastDay }}">
                                            <i class="fa fa-cog mr-5"></i> Rapportini mancanti
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="block">
                            <div class="block-content block-content-full" style="min-height: 350px">
                                <div class="py-20 text-center">
                                    <div class="mb-20">
                                        <i class="fa fa-sticky-note fa-4x text-info"></i>
                                    </div>
                                    <div class="font-size-h4 font-w600">{{ $dashboardStats->monthlyNotes }} note cantiere</div>
                                    <div class="text-muted">Verifica le note inviate</div>
                                    <div class="pt-20">
                                        <a class="btn btn-rounded btn-alt-info" href="{{ route('notes_list') }}?date_from={{ date("01-{$month}-$year") }}&date_to={{ $lastDay }}">
                                            <i class="fa fa-sticky-note mr-5"></i> Controlla note
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="block">
                            <div class="block-content block-content-full" style="min-height: 350px">
                                <div class="py-20 text-center">
                                    <div class="mb-20">
                                        <i class="fa fa-envelope-open fa-4x text-success"></i>
                                    </div>
                                    <div class="font-size-h4 font-w600">{{ $cstReports->count() }} Fogli fine cantiere</div>
                                    <div class="text-muted">Verifica i fogli fine cantiere cliente inviati</div>
                                    <div class="pt-20">
                                        <a class="btn btn-rounded btn-alt-info" href="{{ route('customer_report_list') }}?date_from={{ date("01-{$month}-$year") }}&date_to={{ $lastDay }}">
                                            <i class="fa fa-arrow-right mr-5"></i> Controlla
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Row #3 -->
                </div>

                @if(auth()->user()->canCsvExport())
                <!-- csv export row  -->
                <div class="row invisible" data-toggle="appear">
                    <div class="col-md-12">
                        <div class="block">
                            <div class="block-content block-content-full">
                                <div class="py-20 text-center">
                                    <div class="mb-20">
                                        <i class="fa fa-file-excel-o fa-4x text-info"></i>
                                    </div>

                                    <div class="font-size-h4 font-w600">Genera esportazione XLSX</div>
                                    <div class="text-muted">Crea una esportazione in excel delle ore mensili lavorate di un dipendente.</div>


                                    <form method="POST" action="{{ route('csv_hour_export') }}">
                                        @csrf

                                        <div class="row pt-20">

                                            <div class="offset-4 col-4">
                                                <select id="export-month" name="export_month" class="form-control csv-export">
                                                    <option value="" selected>Seleziona un mese</option>
                                                    @for($i=0; $i<=6; $i++)
                                                        @php
                                                            $todaysDate = \Carbon\Carbon::now();
                                                            $pastMonth = $todaysDate->subMonthsNoOverflow($i);
                                                        @endphp
                                                        <option value="{{ $pastMonth->format("Y-m-01") }}">
                                                            {{ convertMonthName($pastMonth->format('F')) }} {{ $pastMonth->format('Y') }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>


                                            <div class="offset-4 col-4 pt-10">
                                                <select id="employee-id" name="employee_id" class="form-control csv-export">
                                                    <option value="" selected>Seleziona un dipendente</option>
                                                    @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->surname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <button id="csv-export-btn" class="btn btn-rounded mt-10" disabled>
                                                    <i class="fa fa-file-excel-o mr-5"></i> Avvia esportazione
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end csv export row -->
                @endif

                @if(auth()->user()->canGenerateSal())
                <!-- csv export row  -->
                <div class="row invisible" data-toggle="appear">
                    <div class="col-md-12">
                        <div class="block">
                            <div class="block-content block-content-full">
                                <div class="py-20 text-center">
                                    <div class="mb-20">
                                        <i class="fa fa-money fa-4x text-success"></i>
                                    </div>

                                    <div class="font-size-h4 font-w600">Genera esportazione SAL</div>
                                    <div class="text-muted">Crea una esportazione in excel per la fatturazione al cliente.</div>


                                    <form method="POST" action="{{ route('generate_sal') }}">
                                        @csrf

                                        <div class="row pt-20">
                                            <div class="offset-4 col-4">
                                                <select id="sal_export_month" name="export_month" class="form-control sal-export">
                                                    <option value="" selected>Seleziona un mese</option>
                                                    @for($i=0; $i<=6; $i++)
                                                        @php
                                                            $todaysDate = \Carbon\Carbon::now();
                                                            $pastMonth = $todaysDate->subMonthsNoOverflow($i);
                                                        @endphp
                                                        <option value="{{ $pastMonth->format("$year-m-01") }}">
                                                            {{ convertMonthName($pastMonth->format('F')) }} {{ $pastMonth->format('Y') }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>


                                            <div class="offset-4 col-4 pt-10">
                                                <select id="sal_customer_id" name="customer_id" class="form-control sal-export" disabled>
                                                    <option value="" selected>Seleziona un cliente</option>
                                                </select>
                                            </div>


                                            <div class="offset-4 col-4 pt-10">
                                                <select id="sal_building_site_id" name="building_site_id" class="form-control sal-export" disabled>
                                                    <option value="" selected>Seleziona un cantiere</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <button id="sal-export-btn" class="btn btn-rounded mt-10" disabled>
                                                    <i class="fa fa-money mr-5"></i> Genera SAL
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end csv export row -->
                @endif

                <div class="row invisible" data-toggle="appear">
                    <!-- Row #4 -->
                    <div class="col-md-6">
                        <a class="block block-link-shadow overflow-hidden" href="javascript:void(0)">
                            <div class="block-content block-content-full">
                                <i class="si si-pencil fa-2x text-body-bg-dark"></i>
                                <div class="row py-20">
                                    <div class="col-6 text-right border-r">
                                        <div class="invisible" data-toggle="appear" data-class="animated fadeInLeft">
                                            <div class="font-size-h3 font-w600 text-info">{{ $bsStats->total_sites }}</div>
                                            <div class="font-size-sm font-w600 text-uppercase text-muted">Totale cantieri</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="invisible" data-toggle="appear" data-class="animated fadeInRight">
                                            <div class="font-size-h3 font-w600 text-success">{{ $bsStats->open_sites }}</div>
                                            <div class="font-size-sm font-w600 text-uppercase text-muted">Cantieri attivi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a class="block block-link-shadow overflow-hidden" href="javascript:void(0)">
                            <div class="block-content block-content-full">
                                <div class="text-right">
                                    <i class="fa fa-user-circle fa-2x text-body-bg-dark"></i>
                                </div>
                                <div class="row py-20">
                                    <div class="col-6 text-right border-r">
                                        <div class="invisible" data-toggle="appear" data-class="animated fadeInLeft">
                                            <div class="font-size-h3 font-w600 text-info">{{ $cstStats->total_customers }}</div>
                                            <div class="font-size-sm font-w600 text-uppercase text-muted">Totale clienti</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="invisible" data-toggle="appear" data-class="animated fadeInRight">
                                            <div class="font-size-h3 font-w600 text-success">{{ $activeCustomers }}%</div>
                                            <div class="font-size-sm font-w600 text-uppercase text-muted">Clienti attivi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END Row #4 -->
                </div>
                <div class="row invisible" data-toggle="appear">
                    <!-- Row #5 -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-link-shadow text-center" href="{{ route('building-sites.index') }}/?date_from={{ date("01-{$month}-$year") }}&date_to={{ $lastDay }}">
                            <div class="block-content">
                                <p class="mt-5">
                                    <i class="si si-home fa-3x"></i>
                                </p>
                                <p class="font-w600">Cantieri</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-link-shadow text-center" href="{{ route('employees.index') }}">
                            <div class="block-content">
                                <p class="mt-5">
                                    <i class="si si-users fa-3x"></i>
                                </p>
                                <p class="font-w600">Dipendenti</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-link-shadow text-center" href="{{ route('machinery.index') }}">
                            <div class="block-content">
                                <p class="mt-5">
                                    <i class="fa fa-car fa-3x"></i>
                                </p>
                                <p class="font-w600">Macchinari</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-link-shadow text-center" href="#" data-toggle="modal" data-target="#search-modal">
                            <div class="block-content">
                                <p class="mt-5">
                                    <i class="si si-magnifier fa-3x"></i>
                                </p>
                                <p class="font-w600">Ricerca</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-link-shadow text-center" href="{{ route('customers.index') }}?date_from={{ date("01-{$month}-$year") }}&date_to={{ $lastDay }}">
                            <div class="block-content">
                                <p class="mt-5">
                                    <i class="fa fa-user-circle fa-3x"></i>
                                </p>
                                <p class="font-w600">Clienti</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-link-shadow text-center" href="{{ route('user_logout') }}">
                            <div class="block-content">
                                <p class="mt-5">
                                    <i class="si si-lock fa-3x"></i>
                                </p>
                                <p class="font-w600">Disconnetti</p>
                            </div>
                        </a>
                    </div>
                    <!-- END Row #5 -->
                </div>
            </div>
            <!-- END Page Content -->

        </div>
    </div>
    <!-- END Draggable Items with jQueryUI -->
@endsection

@section('footer')
    <script>
        $(document).ready(function() {

            // CSV Export functionality

            @if(auth()->user()->canCsvExport())
            resetCsvFields();

            $('.csv-export').change(function(){
                var error = false;

                // Check all the csv export inputs
                $('.csv-export').each(function() {
                    if (!$('option:selected', this).val()) {
                        error = true;
                    }
                });

                // If all the inputs have a selection, enable the export button
                if (!error) {
                    $('#csv-export-btn').addClass('btn-alt-info');
                    $('#csv-export-btn').removeAttr('disabled');
                } else {
                    $('#csv-export-btn').removeClass('btn-alt-info');
                    $('#csv-export-btn').attr('disabled');
                }
            });
            @endif


            // SAL Export functionality

            @if(auth()->user()->canGenerateSal())
            resetSalFields();

            // Enable customer selection for sal when the user picks a month
            $('#sal_export_month').change(function(){
                if ($('option:selected', this).val()) {
                    $('#sal_customer_id').removeAttr('disabled');

                    // Load the customer's building sites list
                    $.ajax({
                        method: 'GET',
                        url: '{{ route('customers.index') }}?json_format=1&date_from='+$('option:selected', this).val(),
                        success: function(data) {
                            $('.cst-list-value').remove();
                            $(data).each(function(){
                                if (undefined !== typeof $(this)[0]) {
                                    $('#sal_customer_id').append($('<option class="cst-list-value" value="' + $(this)[0].id + '">' + $(this)[0].company_name + '</option>'));
                                }
                            });
                        }
                    });
                } else {
                    resetSalFields();
                }
            });

            // Enable building site selection for sal when the user picks a customer
            $('#sal_customer_id').change(function(){
                if ($('option:selected', this).val()) {
                    $('#sal_building_site_id').removeAttr('disabled');

                    // Load the customer's building sites list
                    $.ajax({
                        method: 'GET',
                        url: '{{ route('building-sites.index') }}?json_format=1&show_closed=true&customer_id=' + $('option:selected', this).val() +
                             '&date_from=' + $('#sal_export_month option:selected').val(),
                        success: function(data) {
                            $('.bs-value').remove();
                            $(data).each(function(){
                                if (undefined !== typeof $(this)[0]) {
                                    $('#sal_building_site_id').append($('<option class="bs-value" value="' + $(this)[0].id + '">' + $(this)[0].site_name + '</option>'));
                                }
                            });
                        }
                    });
                } else {
                    resetSalFields();
                }
            });

            // Enable the SAL export button
            $('#sal_building_site_id').change(function() {
                if ($('option:selected', this).val()) {
                    $('#sal-export-btn').addClass('btn-alt-success');
                    $('#sal-export-btn').removeAttr('disabled');
                } else {
                    $('#sal-export-btn').removeClass('btn-alt-success');
                    $('#sal-export-btn').attr('disabled');
                }
            });
            @endif


        });

        /**
         *
         */
        function resetCsvFields()
        {
            $('.csv-export').val('');
        }

        /**
         *
         */
        function resetSalFields()
        {
            $('.sal-export').val('');
            $('#sal_customer_id').prop('disabled', true);
            $('#sal_building_site_id').prop('disabled', true);
            $('#sal-export-btn').prop('disabled', true);
            $('#sal-export-btn').removeClass('btn-alt-success');
        }
    </script>
@endsection