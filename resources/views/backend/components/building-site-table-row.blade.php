<tr>
    <td class="text-center">{{ $loop->iteration }}</td>
    <td class="font-w600">{{ $bs->site_name }}</td>
    <td class="font-w600">{{ $bs->customer->company_name }}</td>
    <td class="d-none d-sm-table-cell">@if($bs->address and isset($bs->address->route)){{ $bs->address->route }}{{ $bs->address->street_number }}, {{ $bs->address->locality }}@endif</td>
    <td class="d-none d-sm-table-cell">
        @foreach($bs->site_type as $btype)
        <span class="badge badge-info">{{ $btype }}</span>
        @endforeach
        {{-- Privato Edile Industriale Altro --}}
    </td>
    <td class="">
        @foreach($bs->employees()->get() as $employee)
            {{ $employee->name }}<br />
        @endforeach
    </td>
    <td class="text-center">
        <div class="btn-group">
            <?php
        
        $rotta = route('building-sites.show', $bs->id); //hard fix ahahaha

            if($bs->status != 'closed'){

                echo"
                
                <a href='".$rotta."' class='btn btn-sm btn-outline-primary' title='Vedi dettagli'>
                    <i class='fa fa-eye'></i>
                </a>
                
                ";

            }else{

                if(!auth()->user()->isAdmin()){

                    echo"
                <a href='' class='btn btn-sm btn-outline-primary' title='Vedi dettagli' style='color:red;border-color:red;pointer-events: none;cursor: default;'>
                    <i class='fa fa-eye-slash'></i>
                </a>
                ";

                } else {

                    echo"
                
                    <a href='".$rotta."' class='btn btn-sm btn-outline-primary' title='Vedi dettagli'>
                        <i class='fa fa-eye'></i>
                    </a>
                    
                    ";


                }

                
            }
            ?>
            

            @if(Gate::allows('update', $bs) or ('closed' == $bs->status and auth()->user()->isAdmin()))

                <a href="{{ route('building_site_reports', $bs->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-bar-chart"></i>
                </a>
            @endif

            @can('update', $bs)
                <a href="{{ route('building-sites.edit', $bs->id) }}" class="btn btn-sm btn-secondary js-tooltip-enabled"
                   title="@isset($bs->updated_by){{ 'Ultima modifica di: '. $bs->updated_by . ' il: '. $bs->updated_at }}@else{{ 'Modifica' }}@endif">
                    <i class="fa fa-pencil"></i>
                </a>
            @endcan
            @can('delete', $bs)
                <button type="button"
                        class="btn btn-sm btn-outline-danger js-tooltip-enabled delete-row-btn"
                        data-url="{{ route('building-sites.destroy', $bs->id) }}"
                        title="Elimina">
                    <i class="fa fa-times"></i>
                </button>
            @endcan
        </div>
    </td>
</tr>
@if('closed' == $bs->status)
<tr class="bg-info-light">
    <td colspan="6">
        Commessa #{{ $loop->iteration }}, chiusa da {{ $bs->closed_by }} il {{ $bs->closing_date }}
    </td>
</tr>
@endif