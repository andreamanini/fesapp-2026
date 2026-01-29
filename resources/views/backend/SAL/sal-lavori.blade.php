<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table>
    <tbody>

    <tr>
        <td><img src="backend/images/logo-fes-black.png" width="250px" /></td>
        <td></td>
        <td colspan="11" rowspan="3">
            FES S.r.l. - UNIPERSONALE<br />
            <small>Sede legale – Brescia, via Angelo Inganni, 4 (BS) Sede operativa – Lonato del Garda, via Mantova, 75 (BS) Tel.: 030/9914685</small><br />
            <small>www.fes-servizi.it – email info@fes-servizi.it Il presente documento è strettamente riservato al destinatario. La rivelazione dei</small><br />
            <small>contenuti del messaggio può costituire violazione dell'art. 616 c.p., tali contenuti sono tutelati dalle norme sui rapporti tra </small><br />
            <small>difensore e cliente dall'art. 103 c.p.  Le informazioni contenute nel presente documento sono riservate e se ne vieta la divulgazione</small>
        </td>
    </tr>

    <tr>
        <td colspan="4"></td>
        <td></td>
    </tr>

    <tr>
        <td colspan="4"></td>
        <td></td>
    </tr>



    <tr>
        <td colspan="4">SAL AUTORIZZAZIONE A FATTURARE</td>
        <td>CLIENTE</td>
        <td colspan="5">{{ $buildingSite->customer->company_name }}</td>
        <td>MESE</td>
        <td colspan="2">{{ $month }}</td>
        <td>ANNO</td>
        <td>{{ date('Y') }}</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="3">RIF. SAL LAVORI A CORPO</td>
        <td>@if('corpo' == $salType){{ 'X' }}@endif</td>
        <td></td>
        <td colspan="3">RIF. SAL LAVORI €/MQ</td>
        <td>@if('euro' == $salType){{ 'X' }}@endif</td>
        <td></td>
        <td colspan="3">RIF. SAL LAVORI A CONSUNTIVO</td>
        <td>@if('consuntivo' == $salType){{ 'X' }}@endif</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="5">RIF. SAL EVENTUALI OPERE EXTRA RICHIESTE</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="2">NR. OFFERTA FES:</td>
        <td>{{ $buildingSite->quote_number }}</td>
        <td>DEL</td>
        <td colspan="2">{{ $buildingSite->quote_date }}</td>
        <td colspan="3">EVENTUALE NR. O RIF. ORDINE:</td>
        <td colspan="2">{{ $buildingSite->order_number }}</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>


    <tr>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">DESCRIZIONE LAVORI</td>
    </tr>

    @if('euro' == $salType)
        @foreach($reports as $rep)
            @foreach($rep->rows()->get() as $row)
                @php
                    $workText = $rep->getWorkTypeName($row->work_type) . ' ' . $row->{$rep->getWorkTypeFieldName($row->work_type)};

                    $materiale = (!empty($row->materiale) ? ' Materiale ' . $row->materiale : '');
                    $mq = ' QTA ' . $row->qty . ' PER MT ' .
                          (!empty($row->mq_lavorati_x) ? $row->mq_lavorati_x : '1') . ' X ' .
                          (!empty($row->mq_lavorati_y) ? $row->mq_lavorati_y : '1') . ' X ' .
                          (!empty($row->mq_lavorati_z) ? $row->mq_lavorati_z : '1');
                @endphp
                @isset($row->mq_lavorati_tot)
                <tr>
                    <td colspan="2" rowspan="3"></td>
                    <td colspan="10">{{ ($workText ?? ''). $materiale . $mq }}</td>
                    <td>TOT. MQ:</td>
                    <td>{{ $row->mq_lavorati_tot }}</td>
                </tr>
                <tr>
                    <td colspan="10"></td>
                    <td>TOT. €/MQ:</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="10"></td>
                    <td>TOT. A CORPO:</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="15"></td>
                </tr>
                @endisset
            @endforeach
        @endforeach
    @elseif('corpo' == $salType)
        @foreach($cstReports as $cstReport)
            <tr>
                <td colspan="2"></td>
                <td colspan="12">{!! $cstReport->work_description !!}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="14"></td>
        </tr>
    @elseif('consuntivo' == $salType)
        @foreach($cstReports as $cstReport)
            <tr>
                <td colspan="2"></td>
                <td colspan="12">{!! $cstReport->work_description !!}</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="3">TOT. IMPORTO A CONSUNTIVO</td>
                <td colspan="1"></td>
                <td colspan="4"></td>
                <td colspan="2">TOT. A CORPO</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="14"></td>
        </tr>
    @endif


    <tr>
        <td colspan="15">ATTENZIONE: CONFERMARE SEMPRE ALIQUOTA IVA, NEL CASO IN CUI NON SI POSSA APPLICARE REVERSE CHARGE. SE DA ESEGUIRE NOTA DI CREDITO SEGUIRA' ADDEBITO € 150,00</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="8">TOTALE IMPORTO A CORPO CONCORDATO IVA ESCLUSA (IMPONIBILE):</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="8">TOTALE IMPORTO A CORPO CONCORDATO IVA AL 22%:</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="4">REVERSE CHARGE N6.3 DSE/SUB</td>
        <td colspan="4">REVERSE CHARGE N6.7 COM/TER </td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="8">TOTALE IMPORTO DA FATTURARE:</td>
        <td colspan=""></td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="2">PAGAMENTO:</td>
        <td colspan="6">RI.BA. 60 GG. D.F.F.M.</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="15">Necessitiamo di ricevere Vs. timbro e firma per accettazione, nulla ricevendo entro 5 gg. dalla data della presente riterremo accettato il documento nella sua interezza.</td>
    </tr>
    <tr>
        <td colspan="15"></td>
    </tr>



    <tr>
        <td colspan="8">TIMBRO E FIRMA PER ACCETTAZIONE DEL CONTENUTO SOPRA RIPORTATO</td>
        <td colspan="2"></td>
        <td colspan="4" rowspan="6"></td>
    </tr>

    <tr>
        <td colspan="8" rowspan="5"></td>
        <td colspan="2"></td>
    </tr>





    {{--@foreach($reports as $report)--}}
        {{--@php--}}
            {{--$timeStart = new \Carbon\Carbon($report->time_start);--}}
            {{--$timeEnd = new \Carbon\Carbon($report->time_end);--}}
            {{--$travelTime = (null !== $report->travel_time ? (int)$report->travel_time : 0);--}}
            {{--$materials = json_decode($report->materials);--}}
        {{--@endphp--}}


        {{--<tr>--}}
            {{--<td></td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td></td>--}}
        {{--</tr>--}}
    {{--@endforeach--}}
    </tbody>
</table>
</html>