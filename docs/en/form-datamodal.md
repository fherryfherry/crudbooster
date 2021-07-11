# Datamodal Form Type
Showing popup that contains a data table that you can choose

## Code Example
```php
$this->form[] = ['label'=>'Product Name','name'=>'products_id','type'=>'datamodal','datamodal_table'=>'products','datamodal_where'=>'','datamodal_columns'=>'name,description,price','datamodal_columns_alias'=>'Name,Description,Price','required'=>true];	
```
## Legend
| Attribute Name | Example | Description
| -------------- | ----------- | --------- |
| datamodal_table (Required) | E.g : `products` | Table name |
| datamodal_columns (Required) | E.g : `name,description,price` | fields name to show. sparate with comma |
| datamodal_columns_alias (Required) | E.g : `Name,Description,Price` | alias of fields |
| datamodal_where | E.g : `id != 1` | Sql query where |
| datamodal_size | E.g : `large` or `default` | popup size |
| datamodal_height | E.g : `500px` | modal height |
| datamodal_paginate | E.g : `10` | results pagination |
| datamodal_orderby | E.g : `field,desc` | orders results |
| datamodal_search_in | E.g: `field,field2` | search only in specified columns

## What's Next
- [Form Input Type: date](./form-date.md)

## Table Of Contents
- [Back To Index](./index.md)
