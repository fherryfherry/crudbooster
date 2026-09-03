<div>
    <label for="{{ $id }}" class="toggle-button">
        <div class="relative">
            <input id="{{ $id }}" type="{{ $type }}" wire:model.live="{{ $model }}" value="{{$value}}" class="sr-only toggle-input">
            <div class="bg-line toggle-line"></div>
            <div class="bg-dot toggle-dot"></div>
        </div>
    </label>
</div>
