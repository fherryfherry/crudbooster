<div class="form-group">
    <label>Model Many To Many</label>
    <select wire:model.live="columns.{{$key}}.config.modelMany" class="form-control">
        <option value="">- Select a Model -</option>
        @foreach($this->modelList as $model)
            <option value="{{$model}}">{{$model}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">Specify the model many, it represent the center table of relationship</small>
</div>
<div class="form-group">
    <label>First Foreign Key</label>
    <select wire:model="columns.{{$key}}.config.firstFk" class="form-control">
        <option value="">- Select a Field -</option>
        @foreach(($column['config']['modelManyFields']??[]) as $field)
            <option value="{{$field}}">{{$field}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">A foreign key field in pivot table for the current table</small>
</div>
<div class="form-group">
    <label>Second Foreign Key</label>
    <select wire:model="columns.{{$key}}.config.secondFk" class="form-control">
        <option value="">- Select a Field -</option>
        @foreach(($column['config']['modelManyFields']??[]) as $field)
            <option value="{{$field}}">{{$field}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">A foreign key field in pivot table for the display table</small>
</div>
<div class="form-group">
    <label>Display Model</label>
    <select wire:model.live="columns.{{$key}}.config.displayModel" class="form-control">
        <option value="">- Select a Model -</option>
        @foreach($this->modelList as $model)
            <option value="{{$model}}">{{$model}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">A model for display data</small>
</div>
<div class="form-group">
    <label>Display Field</label>
    <select wire:model="columns.{{$key}}.config.displayField" class="form-control">
        <option value="">- Select a Field -</option>
        @foreach(($column['config']['displayModelFields']??[]) as $field)
            <option value="{{$field}}">{{$field}}</option>
        @endforeach
    </select>
    <small class="text-gray-400 text-xs">A field from display model as source to display a data</small>
</div>
