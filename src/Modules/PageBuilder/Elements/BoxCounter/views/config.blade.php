<div x-data="{showCreateQuery: false, createQueryTargetField: 'form.queryBuilder'}">
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
        @include('cb.element::views.query-picker-field', ['field' => 'form.queryBuilder'])
        <div class="form-help">
            You have to define the query in the Query Builder module
        </div>
        @error('form.queryBuilder') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Query Last (Optional)</label>
        @include('cb.element::views.query-picker-field', ['field' => 'form.queryLast'])
        <div class="form-help">
            You have to define the query in the Query Builder module
        </div>
        @error('form.queryLast') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="flex justify-end gap-1">
        <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
        <button class="btn btn-primary" wire:click="save">Save</button>
    </div>
    @include('cb.element::views.query-picker-modal')
</div>
