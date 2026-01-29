
<tr>
    <td class="text-center">
        {{ $loop->iteration }}
    </td>
    <td class="font-w600">@if(null !== $report->employee){{ $report->employee->name }} {{ $report->employee->surname }}@endif</td>
    <td class="d-none d-sm-table-cell">@if(null !== $report->buildingSite){{ $report->buildingSite->site_name }}@endif</td>
    @php
        use Carbon\Carbon;
    @endphp

    <td class="d-none d-sm-table-cell">
        @php
            $createdAt = Carbon::parse($report->created_at);
        @endphp
        @if($createdAt->hour >= 0 && $createdAt->hour <= 7)
            &#9888 <font color='red'>INSERITO DOPO LA MEZZANOTTE</font> &#9888 <br>{{ $report->created_at }}
        @else
            {{ $report->created_at }}
        @endif
    </td>

    <td class="d-none d-sm-table-cell">
        <i class="fa fa-flask" title="litri di gasolio utilizzati"></i> {{ $report->tot_petrol_used }}L <br />
        <!--<i class="fa fa-pencil-square" title="metri quadri lavorati"></i> {{ $report->countMq() }} mq <br />-->
        <i class="fa fa-hourglass-3" title="ore di lavoro"></i> {{ $report->total_working_hours }}h <br />
		@if($report->extra_work_description != '')<i class="fa fa-tag" title="extra"></i> Extra: <br> {{Str::limit( $report->extra_work_description , 50)}} @endif
    </td>
    <td class="text-center">
        <a href="{{ route('show_report', $report->id) }}" class="btn btn-sm btn-outline-primary" title="Vedi dettagli">
            <i class="fa fa-eye"></i>
        </a>

        <a href="{{ route('daily_report_pdf', $report->id) }}" class="btn btn-sm btn-outline-secondary" title="Scarica PDF">
            <i class="fa fa-file-pdf-o"></i>
        </a>

        @can('update', $report)
            <a href="{{ route('reports.update', $report->id) }}" class="btn btn-sm btn-outline-secondary" title="Modifica rapportino">
                <i class="fa fa-pencil"></i>
            </a>
        @endcan

        @can('delete', $report)
            <button type="button"
                    class="btn btn-sm btn-outline-danger js-tooltip-enabled delete-row-btn"
                    data-url="{{ route('delete_employee_report', $report->id) }}"
                    title="Elimina">
                <i class="fa fa-times"></i>
            </button>
        @endcan
    </td>
</tr>