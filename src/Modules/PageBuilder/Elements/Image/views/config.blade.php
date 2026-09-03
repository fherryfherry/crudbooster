<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Title</label>
            <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control" wire:model="form.title">
            <div class="form-help">Enter the title widget</div>
            @error('form.title') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Image</label>
            @if(is_object($imageUpload) && !is_string($imageUpload))
                <img src="{{ $imageUpload->temporaryUrl() }}" class="w-36 h-36 thumbnail">
            @endif
            @if(!is_object($imageUpload) && isset($form['image']) && is_string($form['image']))
                <img src="{{ getStorageUrl($form['image']) }}" class="w-36 h-36 thumbnail">
            @endif
            <input type="file" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control" wire:model="imageUpload">
            <div class="form-help">File supported only: jpg,jpeg,png</div>
            @error('form.imageUpload') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="flex justify-end gap-1">
            <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
