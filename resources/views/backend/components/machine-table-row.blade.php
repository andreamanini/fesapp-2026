<tr>
    <td class="text-center">{{ $machine->machine_number }}</td>
    <td class="font-w600">{{ $machine->machine_name }}</td>
    <td class="font-w600">{{ substr($machine->machine_description, 0, 80) }}</td>
    <td class="text-center">
        @can('update', $machine)
            <a href="{{ route('machinery.edit', $machine->id) }}" class="btn btn-sm btn-secondary" title="Modifica">
                <i class="fa fa-pencil"></i>
            </a>
        @endcan

        @can('delete', $machine)
            <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn"
                    data-url="{{ route('machinery.destroy', $machine) }}"
                    title="Elimina">
                <i class="fa fa-times"></i>
            </button>
        @endcan
    </td>
</tr>