<div>
    <form wire:submit.prevent='save'>
        @csrf
    <div class="form-group">
        <label>Title</label>
        <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse" class="form-control" wire:model="form.title">
        <div class="form-help">Enter the title widget</div>
        @error('form.title') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Paragraph</label>
        <textarea wire:model="form.paragraph" class="form-control" rows="10"></textarea>
        <div class="form-help">Enter the paragraph</div>
        @error('form.paragraph') <div class="form-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="flex justify-end gap-1">
        <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    </form>
</div>
