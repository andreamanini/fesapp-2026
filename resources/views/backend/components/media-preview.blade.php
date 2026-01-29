<div class="dz-preview dz-processing dz-image-preview dz-complete media-item">
    <div class="dz-image">
        @if ('image' == $mediaFile->media_type)
            <img data-dz-thumbnail="" class="media-image" src="{{ asset($mediaFile->getFullPath('thumb')) }}"
                 width="120px" data-media-id="{{ $mediaFile->id }}" />
        @else
            <img data-dz-thumbnail="" src="{{ asset($mediaFile->getFullPath('thumb')) }}" width="120px" />
        @endif
    </div>
    @if(!empty($showCheck))
        <label style="display:none">
            {{ __('content.select') }}
            <input type="checkbox" class="select-img-check" id="checkbox-{{ $mediaFile->id }}" name="media_id[]"
                   value="{{ $mediaFile->id }}" style="opacity:1;width:20px;height:20px" />
        </label>
    @endif
    @if(!empty($showOverlayDelete))
        <div class="dz-details">
            <div class="dz-size">
                <a href="{{ route('tag_image', $mediaFile->id) }}" class="btn btn-primary btn-sm">
                    Note
                </a>
            </div>
            <div class="dz-filename">
                <button class="btn btn-danger btn-sm delete-media"
                        data-url="{{ route('media.destroy', $mediaFile->id) }}">
                    Elimina
                </button>
            </div>
        </div>
    @endif

</div>