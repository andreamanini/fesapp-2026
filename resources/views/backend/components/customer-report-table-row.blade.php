<tr {{ $rowcolor }}>
    <td class="text-center">
        {{ $loop->iteration }}
    </td>
    <td class="font-w600">@if(null !== $report->employee){{ $report->employee->name }} {{ $report->employee->surname }}@endif</td>
    <td class="d-none d-sm-table-cell">{{ $report->buildingSite->site_name }}</td>
    <td class="d-none d-sm-table-cell">{{ $report->created_at }}</td>
    <td class="text-center">
        <a href="{{ route('view_customer_report', $report->id) }}" class="btn btn-sm btn-outline-primary" title="Vedi dettagli">
            <i class="fa fa-eye"></i>
        </a>

        <a href="{{ route('cst_report_pdf', $report->id) }}" class="btn btn-sm btn-outline-secondary" title="Scarica PDF">
            <i class="fa fa-file-pdf-o"></i>
        </a>

        @can('update', $report)
        <a href="{{ route('edit_customer_report', $report->id) }}" class="btn btn-sm btn-outline-secondary" title="Modifica foglio">
            <i class="fa fa-pencil"></i>
        </a>
        @endcan

        @can('delete', $report)
            <button type="button"
                    class="btn btn-sm btn-outline-danger js-tooltip-enabled delete-row-btn"
                    data-url="{{ route('delete_cst_report', $report->id) }}"
                    title="Elimina">
                <i class="fa fa-times"></i>
            </button>
        @endcan
    </td>
</tr>