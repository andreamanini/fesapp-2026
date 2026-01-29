<tr>
    <td class="text-center">
        {{ $loop->iteration }}
    </td>
    <td class="font-w600">{{ $note->employee->name }} {{ $note->employee->surname }}</td>
    <td class="d-none d-sm-table-cell">{{ $note->buildingSite->site_name }}</td>
    <td class="d-none d-sm-table-cell">{{ $note->created_at }}</td>
    <td class="d-none d-sm-table-cell">
        {{ $note->media()->count() }} <i class="fa fa-photo"></i>
    </td>
    <td class="d-none d-sm-table-cell">
        <a href="{{ route('view_note', $note->id) }}" class="btn btn-sm btn-outline-primary" title="Vedi dettagli">
            <i class="fa fa-eye"></i>
        </a>
        @can('update', $note)
        <a href="{{ route('edit_note', $note->id) }}" class="btn btn-sm btn-secondary" title="Modifica nota">
            <i class="fa fa-pencil"></i>
        </a>
        @endcan
        @can('delete', $note)
            <button type="button"
                    class="btn btn-sm btn-outline-danger js-tooltip-enabled delete-row-btn"
                    data-url="{{ route('notes.destroy', $note->id) }}"
                    title="Elimina">
                <i class="fa fa-times"></i>
            </button>
        @endcan
    </td>
</tr>