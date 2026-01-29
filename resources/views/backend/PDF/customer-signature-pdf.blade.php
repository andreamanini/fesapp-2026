<!doctype html>
<html lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>

    <div style="width:100%;background-color:#FFD842;height:100px;position:relative;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents('backend/images/fes-logo.svg')) }}" width="200px" style="position:absolute;padding-top:15px;padding-left:20px;" />
    </div>

    <h1>Foglio fine cantiere</h1>

    <table width="100%">
        <tr>
            <td><strong>CANTIERE:</strong> {{ $customerReport->buildingSite->site_name }}</td>
            <td><strong>DATA:</strong> {{ $customerReport->created_at->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td><strong>LAVORO ESEGUITO PRESSO:</strong></td>
            <td><strong>LAVORO DA FATTURARE A:</strong></td>
        </tr>
        <tr>
            <td>{{ $customerReport->company_name }}<br/>{{ $customerReport->company_address }}<br/>{{ $customerReport->company_city }}</td>
            <td><input type="checkbox" checked/> @if('azienda terza' == $customerReport->billing_to) {{ $customerReport->billing_to_company }} @else Cliente dove è stato eseguito il lavoro @endif</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td><strong>TIPOLOGIA LAVORO</strong></td>
            <td><input type="checkbox" checked/> {{ $customerReport->job_type }}</td>
        </tr>
    </table>
    <br /><br />

    <strong>DESCRIZIONE DEL LAVORO:</strong><br />
    {!! $customerReport->work_description !!}
    
    
    @php
        $reportRows = $customerReport->rows()->get();
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
                #$mqRow = ($row->mq_lavorati_tot * (!empty($row->qty) ? $row->qty : 1));
                $totPartial += $row->mq_lavorati_tot;
            @endphp
            <tr>
                <td>
                    {{ $customerReport->getWorkTypeName($row->work_type) }}
                </td>
                <td>
                    {{ $row->{$customerReport->getWorkTypeFieldName($row->work_type)} }}
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

    </table>
    <br />
    <!--
    <table style="padding-top:30px">
        <tr class="row-header">
            <td>RIEPILOGO LAVORI</td>
            <td>TOTALE</td>
        </tr>
        <tr>
            <td>Strutture sabbiate</td>
            <td>{{ $totSabbiato }} mq</td>
        </tr>
        <tr>
            <td>Strutture verniciate</td>
            <td>{{ $totVerniciato }} mq</td>
        </tr>
        <tr>
            <td>Strutture lavate</td>
            <td>{{ $totLavato }} mq</td>
        </tr>
        <tr>
            <td>Strutture soffiate</td>
            <td>{{ $totSoffiato }} mq</td>
        </tr>
        <tr>
            <td>Strutture intonacate</td>
            <td>{{ $totIntonacato }} mq</td>
        </tr>
        <tr>
            <td>Altro</td>
            <td>{{ $totAltro }} mq</td>
        </tr>
    </table>
    -->
    <h3 style="padding-top:20px">Dichiarazione</h3>

    <table style="padding-top:10px;width:100%">
        <tr>
            <td><strong>IL SOTTOSCRITTO</strong></td>
            <td><strong>DELLA SOCIETÀ</strong></td>
            <td><strong>DICHIARO CHE IL VOSTRO TECNICO</strong></td>
        </tr>
        <tr>
            <td>{{ $customerReport->signature_name }}</td>
            <td>{{ $customerReport->signature_company_name }}</td>
            <td>{{ $customerReport->employee_name }}</td>
        </tr>
    </table>
    <br/>

    <p>HA ESEGUITO I LAVORI LASCIANDO IN PERFETTE CONDIZIONI IL LUOGO DI LAVORO E DI AVER VERIFICATO LA CORRETTEZZA
        DEI DATI SCRITTI NELLA PRESENTE COMMESSA <strong>(COMPRESO METRATURE E/O ORE) E CHE LE LAVORAZIONI SONO STATE
            ESEGUITE
            A REGOLA D'ARTE SENZA NULLA DA CONTESTARE.</strong> (l'eventuale isolamento del luogo di lavoro, salvo diverso
        accordo
        scritto è a carico del Cliente)</p><br/>

    <div class="col-12" align="center">
        <hr />
        <p style="font-size:9px">{!! __('fes.customer_report_legal') !!}</p>
    </div>


    <h3>Note:</h3>
    <p>{{ $customerReport->additional_notes }}</p>

    <h3>Firma del cliente:</h3><br/>
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($customerReport->customer_signature)) }}" width="300px"/>
</body>
</html>