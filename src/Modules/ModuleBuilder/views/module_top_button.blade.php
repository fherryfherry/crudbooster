@php use CrudBooster\Components\Icon\Icon; @endphp
<div class="mt-4">
    <div class="text-right mb-2 flex justify-end gap-1">
        <button title="Click here to build / rebuild the module"
                wire:click="buildModuleConfirm"
                class="btn btn-primary shadow-md"
            @disabled(!$this->validateBuild) >
            {!! Icon::PLAY !!} <span>Re/Build Module</span>
        </button>
    </div>
</div>
