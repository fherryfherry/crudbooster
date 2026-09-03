@php $field = $field ?? 'form.query'; @endphp
<div class="flex items-center gap-1">
    <select wire:model="{{ $field }}" wire:loading.attr="disabled" wire:target="save"
        wire:loading.class="animate-pulse" class="form-control">
        <option value="">- Select a Query -</option>
        @foreach($this->queryBuilderList as $query)
            <option value="{{ $query['id'] }}">{{ $query['name'] }}</option>
        @endforeach
    </select>
    <button type="button" title="Add new query" class="btn btn-outline-light"
        wire:click="$set('showCreateQueryModal', true)"
        @click="createQueryTargetField = '{{ $field }}'; showCreateQuery = true">
        {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
    </button>
    <a href="javascript:" wire:click="$refresh" title="Refresh" class="btn btn-outline-light">
        {!! \CrudBooster\Components\Icon\Icon::REFRESH !!}
    </a>
</div>
