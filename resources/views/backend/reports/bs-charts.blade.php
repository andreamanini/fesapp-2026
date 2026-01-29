@extends('layouts.fes-app')

@section('content')
    @php
        $report = new \App\Report();
        $stringHours = '';
        $totaleOre = 0;

        $stringMeters = '';
        $totaleMetri = 0;

        $currentDate = $dateFrom->copy();
        while ($currentDate <= $dateTo) {
            $day = $currentDate->format('d');
            $hour = $report->calculateMonthlyHours($currentDate->format("Y-m-d 00:00:00"), $currentDate->format("Y-m-d 23:59:59"), null, $buildingSite);
            $meter = $report->calculateMonthlyMeters($currentDate->format("Y-m-d 00:00:00"), $currentDate->format("Y-m-d 23:59:59"), null, $buildingSite);

            if (isset($hour->tot_h)) {
                $stringHours .= $hour->tot_h . ',';
                $totaleOre += $hour->tot_h;
            } else {
                $stringHours .= '0,';
            }

            if (isset($meter->tot_mq)) {
                $stringMeters .= $meter->tot_mq . ',';
                $totaleMetri += $meter->tot_mq;
            } else {
                $stringMeters .= '0,';
            }

            $currentDate->addDay();
        }
    @endphp

    <div class="row clienti">
        <div class="col-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Reportistica cantiere {{ $buildingSite->site_name }}</h3>
                </div>

                <div class="block-content">

                    <div class="row pb-30">
                        <div class="col-7">
                            <label>Visualizza tutti i rapportini di questo cantiere:</label>
                            <a href="{{ route('report_list') }}?building_site_id={{ $buildingSite->id }}" class="">Clicca qui</a>
                        </div>

                        <div class="col-5">
                            <label for="start_date">Data di inizio</label>
                            <input type="text" id="df" value="{{ $dateFrom->format('d-m-Y') }}" readonly style="max-width:100px;margin-right:10px" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label>Totale ore lavoro relative al periodo dal
                                <strong>{{ $dateFrom->format('d-m-Y') }}</strong> al
                                <strong>{{ $dateTo->format('d-m-Y') }}</strong>:</label>
                            <canvas id="hourChart" class="js-chartjs-lines"></canvas>
                            <br/>
                            <p>Totale ore lavoro relative al periodo corrente: {{ $totaleOre }}</p>
                        </div>
                    </div>

                    <div class="row pt-50">
                        <div class="col-12">
                            <label>Totale metri lavorati relativi al periodo corrente:</label>
                            <canvas id="metersChart" class="js-chartjs-lines"></canvas>
                            <br/>
                            <p>Totale metri lavorati relativi al periodo corrente: {{ $totaleMetri }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('backend/js/plugins/chartjs/chart.min.js') }}"></script>
    <script>
        // HOURS CHART
        var ctx = document.getElementById('hourChart').getContext('2d');
        var hourChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @php
                        $currentDate = $dateFrom->copy();
                        $labels = [];
                        while ($currentDate <= $dateTo) {
                            $labels[] = $currentDate->format('d');
                            $currentDate->addDay();
                        }
                        echo implode(",", array_map(function($label) { return "'" . $label . "'"; }, $labels));
                    @endphp
                ],
                datasets: [{
                    label: 'Ore lavorate nel periodo corrente',
                    data: [
                        {!! $stringHours !!}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // METERS CHART
        var ctx = document.getElementById('metersChart').getContext('2d');
        var metersChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @php
                        $currentDate = $dateFrom->copy();
                        $labels = [];
                        while ($currentDate <= $dateTo) {
                            $labels[] = $currentDate->format('d');
                            $currentDate->addDay();
                        }
                        echo implode(",", array_map(function($label) { return "'" . $label . "'"; }, $labels));
                    @endphp
                ],
                datasets: [{
                    label: 'Metri lavorati nel periodo corrente',
                    data: [
                        {!! $stringMeters !!}
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        $(document).ready(function() {
            var dateFormat = "mm-dd-yy",
                from = $("#df")
                    .datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        numberOfMonths: 2,
                        dateFormat: 'dd-mm-yy'
                    })
                .on("change", function() {
                    location.href = '{{ route('building_site_reports', $buildingSite->id) }}?date_from='+$("#df").val();
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
        });
    </script>
@endsection
