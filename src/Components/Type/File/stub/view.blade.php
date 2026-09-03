@php use CrudBooster\Components\Icon\Icon; @endphp
{{-- There are variables $column, $value, and $formData that you can use --}}
<div class="block">
    @if($value)
        @if(getStorageFileExists($value, $column['option']['disk'] ?? null))
            <a href="{{ getStorageUrl($value, null, $column['option']['disk'] ?? null) }}" target="_blank" class="text-sky-600 hover:underline text-sm align-middle flex items-center gap-1">
                {!! Icon::DOWNLOAD !!} Download File {{ strtoupper(pathinfo($value, PATHINFO_EXTENSION))}} ({{ round(getStorageFileSize($value, $column['option']['disk'] ?? null) / 1024, 2)}} KB)
            </a>
        @else
            <div class="text-red-500 text-sm flex items-center gap-1">
                &#9888; File not found: {{ basename($value) }}
            </div>
        @endif
    @endif
</div>
