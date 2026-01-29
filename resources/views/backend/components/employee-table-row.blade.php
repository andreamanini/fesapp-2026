<tr>
    <td class="text-center">{{ $loop->iteration }}</td>
    <td class="font-w600">{{ $employee->name }} {{ $employee->surname }}</td>
    <td class="d-none d-sm-table-cell">{{ $employee->telephone }}</td>
    <td class="d-none d-sm-table-cell">{{ $employee->email }}</td>
    <td class="d-none d-md-table-cell">
        @if('admin' == $employee->role)
            <span class="badge badge-primary">Amministratore</span>
        @elseif('superadmin' == $employee->role)
            <span class="badge badge-primary">Super Amministratore</span>
        @elseif('employee' == $employee->role)
            <span class="badge badge-success">Operaio</span>
        @endif
    </td>
    <td class="text-center">
        @can('update', $employee)
            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-secondary js-tooltip-enabled" title="Modifica">
                <i class="fa fa-pencil"></i>
            </a>
        @endcan

        @can('delete', $employee)
            <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn"
                    data-url="{{ route('employees.destroy', $employee->id) }}"
                    title="Elimina">
                <i class="fa fa-times"></i>
            </button>
        @endcan
    </td>
</tr>