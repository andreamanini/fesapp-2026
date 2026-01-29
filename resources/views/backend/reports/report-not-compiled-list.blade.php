@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Lista Rapportini Mancanti</h3>
                </div>
                
                <div class="block-content">
                    @if(isset($_GET['date_to']) && isset($_GET['date_to']))
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
                    
                    <br>
                    @endif
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th class="text-center"></th>
                            <th>Dipendente</th>
                            <th class="d-none d-sm-table-cell">Data</th>
                            <th class="text-center">Giorno</th>
                            <th class="text-center">Ignora</th>
                        </tr>
                        </thead>
                        <tbody>
                        
                            @php
 
                            $begin = new DateTime($dateFrom->format('Y-m-d'));
                            $end = new DateTime($dateTo->format('Y-m-d'));
                            $today = new DateTime();

                            $interval = DateInterval::createFromDateString('1 day');
                            $period = new DatePeriod($begin, $interval, $end);
                            $report = new \App\Report();
                            $i = 1;
                            $datecolor = true;
                            $actualday = '';
                            foreach ($period as $dt) {
                                if ($dt > $today) continue;
                                $nextday = $dt->format('Y-m-d');
                                if ($nextday != $actualday) $datecolor = !$datecolor;
                                ($datecolor) ? $rowcolor = 'class=table-warning' : $rowcolor = 'class=table-light';
                                $actualday = $nextday;
                                foreach ($employees as $employee) { 
                                    if ($report->checkReportPresence($employee->id,$dt->format('Y-m-d'))) continue;
                                    @endphp
                                    <tr {{ $rowcolor }} id="row{{$i}}">
                                        <td>{{ $i }}</td> 
                                        <td>{{ $employee->name }} {{ $employee->surname }}</td>
                                        <td>{{ $dt->format("d-m-Y") }}</td>
                                        <td>{{ convertDayName($dt->format("l")) }}</td>
                                        <td><a href="{{ route('not_compiled_report') }}?date_from={{$dateFrom->format('d-m-Y')}}&date_to={{$dateTo->format('d-m-Y')}}&ignore_user={{$employee->id}}&ignore_date={{$dt->format('Y-m-d')}}#row{{$i}}"><button type="submit" class="btn btn-danger fes-btn-w">Ignora</button></a></td>
                                    </tr>
                                    @php
                                    $i++;
                                }
                            }
                            
                            $nextday = $end->format('Y-m-d');
                            if ($nextday != $actualday) $datecolor = !$datecolor;
                            ($datecolor) ? $rowcolor = 'class=table-warning' : $rowcolor = 'class=table-light';
                            if ($end <= $today) {
                                foreach ($employees as $employee) { 
                                    if (!$report->checkReportPresence($employee->id,$end->format('Y-m-d'))) {
                                        @endphp
                                        <tr {{ $rowcolor }} id="row{{$i}}">
                                            <td>{{ $i }}</td> 
                                            <td>{{ $employee->name }} {{ $employee->surname }}</td>
                                            <td>{{ $end->format("d-m-Y") }}</td>
                                            <td>{{ convertDayName($end->format("l")) }}</td>
                                            <td><a href="{{ route('not_compiled_report') }}?date_from={{$dateFrom->format('d-m-Y')}}&date_to={{$dateTo->format('d-m-Y')}}&ignore_user={{$employee->id}}&ignore_date={{$dt->format('Y-m-d')}}#row{{$i}}"><button type="submit" class="btn btn-danger fes-btn-w">Ignora</button></a></td>
                                        </tr>
                                        @php
                                        $i++;
                                    }    
                                }
                            }
                            
                            @endphp
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('footer')

    @component('backend.components.delete-modal', [
        'recordName' => 'rapportino'
    ])@endcomponent

    <script>
        $(document).ready(function() {
            var dateFormat = "dd-mm-yy",
                from = $("#df")
                    .datepicker({
                        maxDate: '{{ date('d-m-Y') }}',
                        defaultDate: "+1w",
                        changeMonth: true,
                        numberOfMonths: 2,
                        dateFormat: 'dd-mm-yy',
                    })
                    .on("change", function() {
                        to.datepicker( "option", "minDate", getDate( this ) );
                        location.href = '{{ route('not_compiled_report') }}?date_from=' + $("#df").val() +
                            '&date_to=' + $('#dt').val();
                    }),
                to = $("#dt").datepicker({
                    minDate: '{{ $dateFrom->format('d-m-Y') }}',
                    maxDate: '{{ date('d-m-Y') }}',
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 2,
                    dateFormat: 'dd-mm-yy'
                })
                    .on("change", function() {
                        from.datepicker( "option", "maxDate", getDate( this ) );
                        location.href = '{{ route('not_compiled_report') }}?date_from=' + $("#df").val() +
                            '&date_to=' + $('#dt').val();
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