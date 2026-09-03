<div x-data="{showCreateQuery: false}">
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Title</label>
            <input type="text" wire:loading.attr="disabled" wire:target="save" wire:loading.class="animate-pulse"
                class="form-control" wire:model="form.title">
            <div class="form-help">Enter the title widget</div>
            @error('form.title') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="">Icon</label>
            <livewire:select-icon wire:model="form.icon"/>
        </div>
        <div class="form-group">
            <label>Show All Link</label>
            <input type="text" placeholder="E.g: https://example.com/" wire:loading.attr="disabled" wire:target="save"
                wire:loading.class="animate-pulse" class="form-control" wire:model="form.showAllLink">
            <div class="form-help">
                Enter the show all link
            </div>
            @error('form.showAllLink') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Query</label>
            <div class="flex items-center gap-1">
                <select wire:model="form.query" wire:loading.attr="disabled" wire:target="save"
                    wire:loading.class="animate-pulse" class="form-control">
                    <option value="">- Select a Query -</option>
                    @foreach($this->queryBuilderList as $query)
                    <option value="{{ $query['id'] }}">{{ $query['name'] }}</option>
                    @endforeach
                </select>
                <a href="{{getCmsUrl('query-builder/create')}}" title="Add new query" target="_blank"
                    class="btn btn-outline-light">
                    {!! \CrudBooster\Components\Icon\Icon::PLUS !!}
                </a>
                <a href="javascript:" wire:click="$refresh" title="Refresh" class="btn btn-outline-light">
                    {!! \CrudBooster\Components\Icon\Icon::REFRESH !!}
                </a>
            </div>

            <div class="form-help">
                You have to define the query in the Query Builder module
            </div>
            @error('form.query') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="">Limit Data</label>
            <input type="number" wire:model="form.limit" class="form-control" min="1">
            <div class="form-help">Limit the data to be displayed</div>
            @error('form.limit') <div class="form-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="flex justify-end gap-1">
            <button type="button" class="btn btn-default" @click="closeModal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

<script>
function queryBuilder() {
    return {
        showResultDialog: false,
        testQuery() {
            this.showResultDialog = true;
            if (typeof this.$wire !== 'undefined' && this.$wire.runQuery) {
                this.$wire.runQuery();
            }
            if (typeof anime !== 'undefined') {
                anime({
                    targets: '#results-container',
                    translateY: [100, 0],
                    duration: 1000,
                    easing: 'easeOutExpo'
                });
            }
        },
        closeResultDialog() {
            setTimeout(() => {
                this.showResultDialog = false;
            }, 500);
            if (typeof anime !== 'undefined') {
                anime({
                    targets: '#results-container',
                    translateY: [0, window.innerHeight],
                    easing: 'easeOutExpo'
                });
            }
        },
        init() {
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    this.closeResultDialog();
                }
            });
        }
    }
}
</script>
