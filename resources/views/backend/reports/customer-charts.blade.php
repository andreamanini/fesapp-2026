@extends('layouts.fes-app')

@section('content')
    @php
        $report = new \App\Report();
        $stringHours = '';
        $totaleOre = 0;

        $stringMeters = '';
        $totaleMetri = 0;

        for($d=1; $d<=date('t'); $d++) {
            $day = ($d > 9 ? "'{$d}'" : "'0{$d}'");
            $hour = $report->calculateMonthlyHours(date("Y-m-{$day} 00:00:00"), date("Y-m-{$day} 23:59:59"), $customer);
            $meter = $report->calculateMonthlyMeters(date("Y-m-{$day} 00:00:00"), date("Y-m-{$day} 23:59:59"), $customer);

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
        }
    @endphp
    <div class="row clienti">
        <div class="col-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Reportistica cliente {{ $customer->company_name }}</h3>
                </div>

                <div class="block-content">

                    <div class="row">
                        <div class="col-12">
                            <label>Totale ore lavoro relative al mese corrente:</label>
                            <canvas id="hourChart" class="js-chartjs-lines"></canvas>
                            <br/>
                            <p>Totale ore lavoro relative al mese corrente: {{ $totaleOre }}</p>
                        </div>
                    </div>

                    <div class="row pt-50">
                        <div class="col-12">
                            <label>Totale metri lavorati relativi al mese corrente:</label>
                            <canvas id="metersChart" class="js-chartjs-lines"></canvas>
                            <br/>
                            <p>Totale metri lavorati relativi al mese corrente: {{ $totaleMetri }}</p>
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
                    @for($d=1; $d<=date('t'); $d++)
                        {!! ($d > 9 ? "'{$d}'" : "'0{$d}'") !!}

                        @if($d != date('t'))
                            {{ ',' }}
                        @endif
                    @endfor
                ],
                datasets: [{
                    label: 'Ore lavorate nel mese corrente',
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
                    @for($d=1; $d<=date('t'); $d++)
                        {!! ($d > 9 ? "'{$d}'" : "'0{$d}'") !!}

                        @if($d != date('t'))
                            {{ ',' }}
                        @endif
                    @endfor
                ],
                datasets: [{
                    label: 'Metri lavorati nel mese corrente',
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
    </script>
@endsection