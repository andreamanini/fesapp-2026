<!doctype html>
<html lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>

    <div style="width:100%;background-color:#FFD842;height:100px;position:relative;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents('backend/images/fes-logo.svg')) }}" width="200px" style="position:absolute;padding-top:15px;padding-left:20px;" />
    </div>

    <h1>Rapportino Dipendente</h1>

    <table width="100%">
        <tr>
            <td><strong>Operaio:</strong> {{ $report->employee->name }} {{ $report->employee->surname }}</td>
            <td><strong>Inviato il:</strong> {{ $report->created_at }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Cantiere:</strong>
             {{ $report->buildingSite->site_name }}
                @if(null !== $report->buildingSite->address and isset($report->buildingSite->address->autocomplete))
                    - {{ $report->buildingSite->address->autocomplete }}
                @endif</td>
        </tr>
    </table>
    <br /><br />

    <table width="100%">
        <tr>
            <td><strong>N. Camion:</strong></td>
            <td><strong>Nome del guidatore:</strong></td>
            <td><strong>Orario di inizio lavoro:</strong></td>
            <td><strong>Orario di fine lavoro:</strong></td>
        </tr>
        <tr>
            <td>{{ $report->truck_no }}</td>
            <td>{{ $report->truck_driver_name }}</td>
            <td>{{ $timeStart->format('d-m-Y H:i') }}</td>
            <td>{{ $timeEnd->format('d-m-Y H:i') }}</td>
        </tr>
    </table>
    <br /><br />

    <table width="100%">
        <tr>
            <td><strong>Totale ore viaggio:</strong></td>
            <td><strong>N. pasti e totale speso:</strong></td>
            <td><strong>Totale ore pausa:</strong></td>
            <td><strong>Orario di pausa:</strong></td>
        </tr>
        <tr>
            <td>{{ $report->travel_time ?? 0 }}</td>
            <td>{{ $report->meals_no ?? 0 }}</td>
            <td>{{ $report->total_break_time ?? 0 }}</td>
            <td>{{ $report->break_from_to ?? 'non specificato' }}</td>
        </tr>
    </table>
    <br />

    <p><strong>Totale ore lavorate</strong></p> {{ $report->total_working_hours ?? 0 }} <br />
    <br /><hr />

    <strong>OPERAI IN CANTIERE</strong> <br /> <br />
    @if(null !== $report->employees)
        @foreach(json_decode($report->employees) as $employee)
            {{ $employee }},
        @endforeach
    @endif
    <br /><hr />

    <strong>DESCRIZIONE LAVORO</strong> <br /> <br />
    @php
        $job = json_decode($report->job_details);
    @endphp

    <table width="100%">
        <tr>
            <td><strong>Coperture -- Ore:</strong> {{ $job->job_coperture ?? '0' }}</td>
            <td><strong>Stuccature -- Ore:</strong> {{ $job->job_stuccature ?? '0' }}</td>
            <td><strong>Carteggiature -- Ore:</strong> {{ $job->job_carteggiatura ?? '0' }}</td>
            <td><strong>Lavaggio -- Ore:</strong> {{ $job->job_lavaggio ?? '0' }}</td>
        </tr>
        <tr>
            <td><strong>Sabbiatura -- Ore:</strong> {{ $job->job_sabbiatura ?? '0' }}</td>
            <td><strong>Verniciatura -- Ore:</strong> {{ $job->job_verniciatura ?? '0' }}</td>
            <td><strong>Intonaco -- Ore:</strong> {{ $job->job_intonaco ?? '0' }}</td>
        </tr>
        <tr>
            <td colspan="4">
                <strong>Altro -- Ore:</strong> {{ $job->job_other ?? '0' }}
                @isset($job->job_other_text){{ $job->job_other_text }}@endisset
            </td>
        </tr>
    </table>
    <br />

    <h5>Descrizione lavori eseguiti</h5>
    <p>{{ $report->work_description ?? 'Nessuna descrizione inserita.' }}</p>
    <br /><hr />

    <!--
    @php
        $reportRows = $report->rows()->get();
        $totPartial = 0;
    @endphp
    <table width="100%">
        <tr>
            <td><strong>TIPO LAVORO</strong></td>
            <td><strong>STRUTTURA</strong></td>
            <td><strong>MATERIALE</strong></td>
            <td><strong>QTA</strong></td>
            <td><strong>MISURA (in metri)</strong></td>
            <td><strong>TOTALE</strong></td>
        </tr>
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
                <td>
                    {{ $report->getWorkTypeName($row->work_type) }}
                </td>
                <td>
                    {{ $row->{$report->getWorkTypeFieldName($row->work_type)} }}
                </td>
                <td>
                    {{ $row->materiale }}
                </td>
                <td>
                    {{ $row->qty }}
                </td>
                <td>
                    {{ $row->mq_lavorati_x }}
                    X
                    {{ $row->mq_lavorati_y }}
                    X
                    {{ $row->mq_lavorati_z }}
                </td>
                <td>
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

    </table>
    <br /><hr />
    -->

    @php
        $equipment = json_decode($report->equipment);
    @endphp
    <h5>ATTREZZATURA</h5>

    <table width="100%">
        <tr>
            <td><strong>Idropulitrici N°</strong></td>
            <td><strong>Intonacatrici N°</strong></td>
            <td><strong>Airmix N°</strong></td>
        </tr>
        <tr>
            <td>{{ $equipment->equipment_idropulitrici ?? '---' }}</td>
            <td>{{ $equipment->equipment_intonacatrici ?? '---' }}</td>
            <td>{{ $equipment->equipment_airmix ?? '---' }}</td>
        </tr>
        <tr>
            <td><strong>Airless N°</strong></td>
            <td><strong>Ponteggi Colore</strong></td>
            <td><strong>Scale Colore</strong></td>
        </tr>
        <tr>
            <td>{{ $equipment->equipment_airless ?? '---' }}</td>
            <td>{{ $equipment->equipment_ponteggi ?? '---' }}</td>
            <td>{{ $equipment->equipment_scale_colore ?? '---' }}</td>
        </tr>
        <tr>
            <td><strong>Carteggiatrici</strong></td>
            <td><strong>Strumentazione</strong></td>
        </tr>
        <tr>
            <td>{{ $equipment->equipment_carteggiatrici ?? '---' }}</td>
            <td>{{ $equipment->equipment_strumentazione ?? '---' }}</td>
        </tr>
    </table>

    <br /><hr />



    <h5>LAVORI EXTRA</h5>
    @if(null !== $report->extra_work_description)
        <p style="color:blue"><strong>{{ $report->extra_work_description }}</strong></p>
    @else
        <p>Nessuna lavorazione extra da segnalare.</p>
    @endif

    <br /><strong>Tempo perso / Ore di fermo</strong><br />
    {{ $report->time_lost ?? '0' }}

    <br /><hr />


    @php
        $materials = json_decode($report->materials);
    @endphp
    <h5>LAVORI EXTRA</h5>

    <table width="100%">
        <tr>
            <td><strong>Gasolio camion Lt</strong></td>
            <td><strong>Gasolio compressore Lt</strong></td>
            <td><strong>Gasolio altro Lt</strong></td>
            <td><strong>Diluente Lt</strong></td>
        </tr>
        <tr>
            <td>{{ $materials->materials_gasolio_camion ?? '---' }}</td>
            <td>{{ $materials->materials_gasolio_compressore ?? '---' }}</td>
            <td>{{ $materials->materials_gasolio_altro ?? '---' }}</td>
        </tr>
        <tr>
            <td><strong>Diluente Lt</strong></td>
            <td><strong>Intonacatrici N</strong></td>
            <td><strong>Big bag N</strong></td>
            <td><strong>Km Giornalieri</strong></td>
        </tr>
        <tr>
            <td>{{ $materials->materials_diluente ?? '---' }}</td>
            <td>{{ $materials->materials_intonacatrici ?? '---' }}</td>
            <td>{{ $materials->materials_big_bag ?? '---' }}</td>
            <td>{{ $materials->materials_km_giornalieri ?? '---' }}</td>
        </tr>
        <tr>
            <td><strong>Sacchi N</strong></td>
            <td><strong>Descrizione</strong></td>
        </tr>
        <tr>
            <td>{{ $materials->materials_sacchi ?? '---' }}</td>
            <td>{{ $materials->materials_sacchi_descr ?? '---' }}</td>
        </tr>
        <tr>
            <td><strong>Latte N</strong></td>
            <td><strong>Descrizione</strong></td>
        </tr>
        <tr>
            <td>{{ $materials->materials_latte ?? '---' }}</td>
            <td>{{ $materials->materials_latte_descr ?? '---' }}</td>
        </tr>
        <tr>
            <td><strong>Altro</strong></td>
            <td><strong>Spese Extra</strong></td>
        </tr>
        <tr>
            <td>{{ $materials->materials_other ?? '---' }}</td>
            <td>{{ $report->extra_expenses ?? '---' }}</td>
        </tr>
    </table>

</body>
</html>
