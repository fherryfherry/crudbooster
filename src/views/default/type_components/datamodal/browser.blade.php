<!-- Bootstrap 3.3.2 -->
<link href="{{ asset("vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css"/>
<!-- Font Awesome Icons -->
<link href="{{asset("vendor/crudbooster/assets/adminlte/font-awesome/css")}}/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<!-- Ionicons -->
<link href="{{asset("vendor/crudbooster/ionic/css/ionicons.min.css")}}" rel="stylesheet" type="text/css"/>
<!-- Theme style -->
<link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css"/>

@include('crudbooster::admin_template_plugins')

<?php
$name = Request::get('name_column');
$coloms_alias = explode(',', 'ID,'.Request::get('columns_name_alias'));
if (count($coloms_alias) < 2 && isset($columns)) {
    $coloms_alias = $columns;
}
?>
<form method='get' action="">
    {!! CRUDBooster::getUrlParameters(['q']) !!}
    <input type="text" placeholder="{{cbLang('datamodal_search_and_enter')." ".Request::get('search_in')}}" name="q" title="{{cbLang('datamodal_enter_to_search')}}"
           value="{{Request::get('q')}}" class="form-control">
</form>

<table id='table_dashboard' class='table table-striped table-bordered table-condensed' style="margin-bottom: 0px">
    <thead>
    @foreach($coloms_alias as $col)
        <th>{{ $col }}</th>
    @endforeach
    <th width="5%">{{cbLang('datamodal_select')}}</th>
    </thead>
    <tbody>
    @foreach($result as $row)
        <tr>
            @foreach($columns as $col)
                @php
                $img_extension = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                $ext = isset($row)&&isset($col) ? pathinfo($row->$col, PATHINFO_EXTENSION) : null;
                if ($ext && in_array($ext, $img_extension)) {
                    echo "<td><a href='".asset($row->$col)."' data-lightbox='roadtrip'><img src='".asset($row->$col)."' width='50px' height='30px'/></a></td>";
                } else {
                    echo "<td>".Str::of(strip_tags($row->$col))->limit(50)."</td>";
                }
                @endphp
            @endforeach
            <?php
            $select_data_result = [];
            $select_data_result['datamodal_id'] = $row->id;
            $select_data_result['datamodal_label'] = $row->{$columns[1]} ?: $row->id;
            $select_data = Request::get('select_to');
            if ($select_data) {
                $select_data = explode(',', $select_data);
                if ($select_data) {
                    foreach ($select_data as $s) {
                        $s_exp = explode(':', $s);
                        $field_name = $s_exp[0];
                        $target_field_name = $s_exp[1];
                        $select_data_result[$target_field_name] = $row->$field_name;
                    }
                }
            }
            ?>
            <td><a class='btn btn-primary' href='javascript:void(0)' onclick='parent.selectAdditionalData{{$name}}({!! json_encode($select_data_result) !!})'><i
                            class='fa fa-check-circle'></i> {{cbLang('datamodal_select')}}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
<div align="center">{!! str_replace("/?","?",$result->appends(Request::all())->render()) !!}</div>
