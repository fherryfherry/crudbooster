{{-- There are variables $column, $value, and $formData that you can use --}}
<div class="block">
    @if($value)
        @if(is_array($value))
            <div class="space-x-2">
            @foreach($value as $image)
                @if(getStorageFileExists($image))
                    <a href="javascript:" @click="openThumbnail = true; thumbnailSrc = '{{ getStorageUrl($image) }}'">
                        <img alt="{{$column['label']}}" title="{{$column['label']}}" class="form-image-preview inline-block"
                            src="{{ getStorageUrl($image) }}">
                    </a>
                @else
                    <div class="inline-block">
                        <img alt="Image not found" title="Image not found" class="form-image-preview inline-block"
                            src="{{ config('cb.no_image_browse', 'https://placehold.co/48') }}">
                        <div class="text-xs text-red-500 mt-1">{{ basename($image) }}</div>
                    </div>
                @endif
            @endforeach
            </div>
        @else
            @if(getStorageFileExists($value))
                <a href="javascript:" @click="openThumbnail = true; thumbnailSrc = '{{ getStorageUrl($value) }}'">
                    <img alt="{{$column['label']}}" title="{{$column['label']}}" class="form-image-preview"
                        src="{{ getStorageUrl($value) }}">
                </a>
            @else
                <div class="inline-block">
                    <img alt="Image not found" title="Image not found" class="form-image-preview"
                        src="{{ config('cb.no_image_browse', 'https://placehold.co/48') }}">
                    <div class="text-xs text-red-500 mt-1">{{ basename($value) }}</div>
                </div>
            @endif
        @endif
    @else
        <span class="italic">No image available</span>
    @endif
</div>
@once
    @include('cb.themes::components.imagepreview')
@endonce
