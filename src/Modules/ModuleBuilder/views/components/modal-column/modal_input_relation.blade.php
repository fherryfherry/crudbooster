<div class="form-group">
    <label>Relation Field</label>
    <select wire:model="columns.{{$key}}.config.key" class="form-control">
        <option value="">- Select a Field -</option>
        @foreach($this->tableFields as $field)
            <option value="{{$field}}">{{$field}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">A field from current table that want to join / relate</small>
</div>
<div class="form-group">
    <label>Target Model</label>
    <select wire:model.live="columns.{{$key}}.config.model" class="form-control">
        <option value="">- Select a Model -</option>
        @foreach($this->modelList as $model)
            <option value="{{$model}}">{{$model}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">Specify the model of target table to join</small>
</div>
<div class="form-group">
    <label>Display Field</label>
    <select wire:model="columns.{{$key}}.config.displayField" @disabled(!($column['config']['modelFields']??null)) class="form-control">
        <option value="">- Select a Field -</option>
        @foreach(($column['config']['modelFields']??[]) as $field)
            <option value="{{$field}}">{{$field}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">Specify the field from target table to set as display column</small>
</div>
