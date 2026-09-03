<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Title</label>
            <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse"
                class="form-control" wire:model="form.title">
            <div class="form-help">Enter the title widget</div>
            @error('form.title') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <!-- config for apiKey -->
        <div class="form-group">
            <label>API Key</label>
            <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse"
                class="form-control" wire:model.live.debounce.300ms="form.apiKey">
            <div class="form-help">Enter the API Key</div>
            @error('form.apiKey') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Find a Place</label>
            <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse"
                class="form-control" wire:model.live.debounce.800ms="form.place">
        </div>
        <!-- config zoom level -->
        <div class="form-group">
            <label>Zoom Level</label>
            <input type="number" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse"
                class="form-control" max="15" wire:model.live.debounce.500ms="form.zoom">
            <div class="form-help">Enter the zoom level</div>
            @error('form.zoom') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <!-- config height -->
        <div class="form-group">
            <label>Height (px)</label>
            <input type="number" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse"
                class="form-control" wire:model="form.height">
            <div class="form-help">Enter the height of the map</div>
            @error('form.height') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="">Map</label>
            @if(isset($form['place']))
            <iframe :src="$wire.form.mapUrl" width="100%" height="100%" frameborder="0" style="border:0;"
                allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
            @endif
        </div>

        <div class="flex justify-end gap-1">
            <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>