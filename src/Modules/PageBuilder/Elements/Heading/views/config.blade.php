<div>
    <div class="form-group">
        <label>Title</label>
        <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control" wire:model="form.title">
        <div class="form-help">Enter the title widget</div>
        @error('form.title') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Heading</label>
        <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control" wire:model="form.heading">
        <div class="form-help">Enter the heading</div>
        @error('form.heading') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="flex justify-end gap-1">
        <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
        <button class="btn btn-primary" wire:click="save">Save</button>
    </div>
</div>
