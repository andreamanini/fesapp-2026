<tr>
    <td class="text-center">{{ $loop->iteration }}</td>
    <td class="font-w600">{{ $employee->name }} {{ $employee->surname }}</td>
    <td class="d-none d-sm-table-cell">{{ $employee->telephone }}</td>
    <td class="d-none d-sm-table-cell">{{ $employee->email }}</td>
    <td class="text-center">
        <a href="{{ route('employees.view_sites', $employee->id) }}" class="btn btn-sm btn-secondary js-tooltip-enabled" title="Visualizza Cantieri">
            <i class="fa fa-briefcase"></i>
        </a>
    </td>
</tr>