<div>
    <div class="form-group">
        <label>Title</label>
        <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control" wire:model="form.title">
        <div class="form-help">Enter the title box counter</div>
        @error('form.title') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Icon SVG</label>
        <livewire:select-icon wire:model="form.icon" />
        <div class="form-help">Select an icon</div>
        @error('form.icon') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Query Actual</label>
        <div class="flex items-center gap-1">
            <select wire:model="form.queryBuilder" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control">
                <option value="">- Select a Query -</option>
                @foreach($this->queryBuilderList as $query)
                    <option value="{{ $query['id'] }}">{{ $query['name'] }}</option>
                @endforeach
            </select>
            <a href="{{getCmsUrl('query-builder/create')}}" title="Add new query" target="_blank" class="btn btn-outline-light">
                {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
            </a>
            <a href="javascript:" wire:click="$refresh" title="Refresh" class="btn btn-outline-light">
                {!! \CrudBooster\Components\Icon\Icon::REFRESH !!}
            </a>
        </div>

        <div class="form-help">
            You have to define the query in the Query Builder module
        </div>
        @error('form.queryBuilder') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Query Last (Optional)</label>
        <div class="flex items-center gap-1">
            <select wire:model="form.queryLast" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control">
                <option value="">- Select a Query -</option>
                @foreach($this->queryBuilderList as $query)
                    <option value="{{ $query['id'] }}">{{ $query['name'] }}</option>
                @endforeach
            </select>
            <a href="{{getCmsUrl('query-builder/create')}}" title="Add new query" target="_blank" class="btn btn-outline-light">
                {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
            </a>
            <a href="javascript:" wire:click="$refresh" title="Refresh" class="btn btn-outline-light">
                {!! \CrudBooster\Components\Icon\Icon::REFRESH !!}
            </a>
        </div>
        <div class="form-help">
            You have to define the query in the Query Builder module
        </div>
        @error('form.queryLast') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="flex justify-end gap-1">
        <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
        <button class="btn btn-primary" wire:click="save">Save</button>
    </div>
</div>
