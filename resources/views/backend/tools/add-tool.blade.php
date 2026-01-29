@extends('layouts.fes-app')

@section('header')
    <link rel="stylesheet" href="{{ asset('backend/js/plugins/dropzonejs/dist/dropzone.css') }}"/>
@endsection

@section('content')
<div class="row utenti-dipendenti">
    <div class="col-lg-12">
        <!-- Bootstrap Register -->
        <div class="block block-themed">
            <div class="block-header fes-gr-colr">
                <h3 class="block-title font-w700">Attrezzatura</h3>
            </div>
            <div class="block-content">
                <form method="POST"
                      @if (isset($machinery->id))
                      action="{{ route('machinery.update', $machinery->id) }}"
                      @else
                      action="{{ route('machinery.store') }}"
                      @endif>
                    @csrf

                    @if (isset($machinery->id)){{ method_field('PATCH') }}@endif

                    <input type="hidden" name="machine_type" value="tool" />

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group row">
                        <div class="col-6">
                            <label for="machine_name">Nome attrezzatura</label>
                            <input type="text" class="form-control" id="machine_name"
                                   name="machine_name"
                                   value="{{ $machinery->machine_name ?? old('machine_name') }}"
                                   placeholder="Inserisci il nome del mezzo.." required>
                        </div>
                        <div class="col-6">
                            <label for="machine_number">Numero attrezzatura</label>
                            <input type="text" class="form-control" id="machine_number"
                                   name="machine_number"
                                   value="{{ $machinery->machine_number ?? old('machine_number') }}"
                                   placeholder="Inserisci il Numero del mezzo..">
                        </div>
                    </div>

                    <div class="tab-pane" id="wizard-progress-step2" role="tabpanel">
                        <div class="form-group">
                            <label for="machine_description">Descrizione aggiuntiva</label>
                            <textarea class="form-control" id="machine_description"
                                      name="machine_description" rows="8">{{ $machinery->machine_description ?? old('machine_description') }}</textarea>
                        </div>
                    </div>

                    @isset($machinery)
                    <h6>Inserisci foto macchinario</h6>
                    <div class="form-group row">
                        <div class="col-12">

                            <!-- DropzoneJS Container -->
                            <div id="dropzonediv" class="dropzone sortable">
                                @foreach($machineryMedia as $media)
                                    @component('backend.components.media-preview', [
                                        'mediaFile' => $media,
                                        'showOverlayDelete' => true
                                    ])@endcomponent
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endisset


                    <div class="form-group row">
                        <div class="col-12 btn-right">
                            <a href="{{ route('machinery.index') }}" class="btn btn-alt-secondary">
                                <i class="fa fa-remove mr-5"></i> Annulla
                            </a>
                            @if(auth()->user()->can('create', \App\Machinery::class) or
                             (isset($machinery) and auth()->user()->can('update', $machinery)))
                            <button type="submit" class="btn btn-success fes-btn-w">
                                <i class="fa fa-save mr-5"></i> Salva
                            </button>
                            @endif
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    @isset($machinery)
    <script>
        const currentRecord = {{ $machinery->id }};
    </script>
    <script src="{{ asset('backend/js/plugins/dropzonejs/dropzone.min.js') }}"></script>
    <script src="{{ asset('backend/js/edit-machinery.js') }}"></script>
    @endisset
@endsection