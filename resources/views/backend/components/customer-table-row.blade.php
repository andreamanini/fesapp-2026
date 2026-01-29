<tr>
    <td class="text-center">{{ $loop->iteration }}</td>
    <td class="font-w600">{{ $customer->company_name }}</td>
    <td class="font-w600">{{ $customer->email }}</td>
    <td class="d-none d-sm-table-cell">{{ $customer->telephone }}</td>
    <td class="text-center">

        {{-- nascosto su richiesta del cliente<a href="{{ route('customer_reports', $customer->id) }}" class="btn btn-sm btn-secondary">--}}
            {{--<i class="fa fa-bar-chart"></i>--}}
        {{--</a>--}}

        @can('update', $customer)
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-secondary"
               title="Modifica">
                <i class="fa fa-pencil"></i>
            </a>
        @endcan

        @can('delete', $customer)
            <button type="button" class="btn btn-sm btn-outline-danger delete-row-btn"
                    data-url="{{ route('customers.destroy', $customer) }}"
                    title="Elimina">
                <i class="fa fa-times"></i>
            </button>
        @endcan
    </td>
</tr>