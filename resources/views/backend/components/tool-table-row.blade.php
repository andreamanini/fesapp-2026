<tr>
    <td class="text-center">{{ $tool->machine_number }}</td>
    <td class="font-w600">{{ $tool->machine_name }}</td>
    <td class="font-w600">{{ substr($tool->machine_description, 0, 80) }}</td>
    <td class="text-center">
        @can('update', $tool)
            <a href="{{ route('edit_tool', $tool->id) }}" class="btn btn-sm btn-secondary" title="Modifica">
                <i class="fa fa-pencil"></i>
            </a>
        @endcan

        @can('delete', $tool)
            <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn"
                    data-url="{{ route('machinery.destroy', $tool) }}"
                    title="Elimina">
                <i class="fa fa-times"></i>
            </button>
        @endcan
    </td>
</tr>