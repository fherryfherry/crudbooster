{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<div x-data="chipSelect_{{$column['key']}}( @entangle('formData.' . $column['key']) )" class="w-full mx-auto mt-2">
    <div class="relative">
        <select x-ref="select" @change="addChip()" class="hidden"></select>
        <div class="flex flex-wrap items-center border border-gray-300 dark:border-gray-600 rounded p-1 dark:bg-gray-800">
            <template x-for="(chip, index) in chips" :key="index">
                <div class="flex items-center bg-sky-500 text-sm text-white dark:bg-sky-700 dark:text-gray-200 rounded-full px-3 py-1 m-1">
                    <span x-text="chip.label"></span>
                    <button wire:loading.remove @click="removeChip(index)" class="ml-2 text-white">&times;</button>
                </div>
            </template>
            <input disabled wire:loading wire:target="formSave" class="flex-1 p-2 text-sm text-gray-500 border-none focus:outline-0 bg-gray-100 dark:bg-gray-700 cursor-not-allowed" placeholder="{{sprintf('Add %s', $column['label'])}}">
            <input @readonly($column['readonly'] ?? false) wire:target="formSave" wire:loading.remove x-model="inputValue" @click="filterSuggestions()" @input="filterSuggestions()" @click.away="filteredSuggestions = []" @keydown.enter.prevent class="flex-1 p-2 text-sm text-gray-500 border-none focus:outline-0 dark:bg-gray-800 dark:text-gray-200"
                   @if(isset($column['isOnChange']) && $column['isOnChange']) @change="$wire.__onChangeFormField('{{$column['key']}}', $event.target.value)" @endif
                   placeholder="{{sprintf('Add %s', $column['label'])}}">
        </div>
        <div x-show="filteredSuggestions.length > 0" class="absolute bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded mt-1 w-full z-10">
            <template x-for="(suggestion, index) in filteredSuggestions" :key="index">
                <div @click="selectSuggestion(suggestion)" class="p-2 text-sm text-gray-500 dark:text-gray-200 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-300 dark:border-gray-600" x-text="suggestion.label"></div>
            </template>
        </div>
    </div>
</div>

<script>
    function chipSelect_{{$column['key']}}(selectChips) {
        return {
            chips: selectChips,
            inputValue: '',
            suggestions: @json($column['option']['dataSelect'] ?? []),
            filteredSuggestions: [],
            addChip() {
                if (this.inputValue.trim() !== '' && Array.isArray(this.chips) && !this.chips.some(chip => chip.label === this.inputValue)) {
                    const selectedSuggestion = this.suggestions.find(suggestion => suggestion.label === this.inputValue);
                    if (selectedSuggestion) {
                        this.chips.push(selectedSuggestion);
                        this.inputValue = '';
                        this.filteredSuggestions = [];
                    }
                }
            },
            removeChip(index) {
                this.chips.splice(index, 1);
            },
            filterSuggestions() {
                this.filteredSuggestions = this.suggestions.filter(suggestion =>
                    suggestion.label.toLowerCase().includes(this.inputValue.toLowerCase()) && Array.isArray(this.chips) &&
                    !this.chips.some(chip => chip.key === suggestion.key)
                );
            },
            selectSuggestion(suggestion) {
                this.inputValue = suggestion.label;
                this.addChip();
            }
        }
    }
</script>
