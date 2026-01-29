<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table>
    <tbody>
        <tr>
            <td>CLIENTE</td>
            <td>{{ $buildingSite->customer->company_name }}</td>
        </tr>
        <tr>
            <td>CANTIERE</td>
            <td>{{ $buildingSite->site_name }}</td>
        </tr>
        <tr>
            <td></td>
        </tr>
        @foreach($reports as $report)
            @php
                $timeStart = new \Carbon\Carbon($report->time_start);
                $timeEnd = new \Carbon\Carbon($report->time_end);
                $travelTime = (null !== $report->travel_time ? (int)$report->travel_time : 0);
                $materials = json_decode($report->materials);
            @endphp
            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">GIORNO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $timeStart->format('d-m-Y') }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">DIPENDENTE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ (null !== $report->employee() ? $report->employee->name . ' ' . $report->employee->surname : '') }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">ORA INIZIO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $timeStart->format('H:i') }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">ORA FINE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $timeEnd->format('H:i') }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td colspan="7" style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>


            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">IDENTIFICAZIONE VOCI ORE TOTALI</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE ORE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $report->total_working_hours }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE ORE VIAGGIO ANDATA</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $travelTime }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE ORE VIAGGIO RITORNO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE ORE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $report->total_working_hours+$travelTime }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td colspan="7" style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>


            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">IDENTIFICAZIONE VOCE VIAGGI</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE KM ANDATA</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE KM RITORNO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE KM</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE LT. GASOLIO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $report->tot_petrol_used }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE LT. GASOLIO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $report->tot_petrol_used }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TELEPASS</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOT. PEDAGGIO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td colspan="7" style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>



            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">IDENTIFICAZIONE VOCE TRASFERTE - VITTO - ALLOGGIO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">PASTI</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ str_replace('"', '', $report->meals_no) }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOT. PASTI</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TRASFERETE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOT. TRASFERTE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">ALLOGGIO (ALBERGO)</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOT. ALBERGO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td colspan="7" style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>



            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">IDENTIFICAZIONE VOCE PLE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">EVENTUALE PLE TIPO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">DURATA NOLO GG.</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">NOLO PLE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">GASOLIO PLE</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_gasolio_altro ?? '0' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">TOTALE LT. GASOLIO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_gasolio_altro ?? '0' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td colspan="7" style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>


            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">IDENTIFICAZIONE VOCE MATERIALE DI CONSUMO</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">Latte N</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_latte ?? '' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;" colspan="2">{{ $materials->materials_latte_descr ?? '' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">Diluente Lt</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_diluente ?? '' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;" colspan="2"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">Big bag N</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_big_bag ?? '' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;" colspan="2"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">Sacchi N</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_sacchi ?? '' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;" colspan="2">{{ $materials->materials_sacchi_descr ?? '' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">Intonacatrici N</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_intonacatrici ?? '' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;" colspan="2"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>
            <tr>
                <td colspan="7" style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>



            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">IDENTIFICAZIONE VOCE ALTRI COSTI</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">ALTRI COSTI</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $materials->materials_other ?? 'Nessuno' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;" colspan="2"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>

            <tr>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">SPESE EXTRA</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;">{{ $report->extra_expenses ?? 'Nessuna' }}</td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;" colspan="2"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
                <td style="background-color:#e7eff9;border:1px solid #d1d1d1;"></td>
            </tr>


            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
</html>