@foreach($addaction as $a)
    <?php
    foreach ($row as $key => $val) {
        $a['url'] = str_replace("[".$key."]", $val, $a['url']);
    }

    $confirm_box = '';
    if (isset($a['confirmation']) && ! empty($a['confirmation']) && $a['confirmation']) {

        $a['confirmation_title'] = ! empty($a['confirmation_title']) ? $a['confirmation_title'] : cbLang('confirmation_title');
        $a['confirmation_text'] = ! empty($a['confirmation_text']) ? $a['confirmation_text'] : cbLang('confirmation_text');
        $a['confirmation_type'] = ! empty($a['confirmation_type']) ? $a['confirmation_type'] : 'warning';
        $a['confirmation_showCancelButton'] = empty($a['confirmation_showCancelButton']) ? 'true' : 'false';
        $a['confirmation_confirmButtonColor'] = ! empty($a['confirmation_confirmButtonColor']) ? $a['confirmation_confirmButtonColor'] : '#DD6B55';
        $a['confirmation_confirmButtonText'] = ! empty($a['confirmation_confirmButtonText']) ? $a['confirmation_confirmButtonText'] : cbLang('confirmation_yes');;
        $a['confirmation_cancelButtonText'] = ! empty($a['confirmation_cancelButtonText']) ? $a['confirmation_cancelButtonText'] : cbLang('confirmation_no');;
        $a['confirmation_closeOnConfirm'] = empty($a['confirmation_closeOnConfirm']) ? 'true' : 'false';

        $confirm_box = '
        swal({   
            title: "'.$a['confirmation_title'].'",
            text: "'.$a['confirmation_text'].'",
            type: "'.$a['confirmation_type'].'",
            showCancelButton: '.$a['confirmation_showCancelButton'].',
            confirmButtonColor: "'.$a['confirmation_confirmButtonColor'].'",
            confirmButtonText: "'.$a['confirmation_confirmButtonText'].'",
            cancelButtonText: "'.$a['confirmation_cancelButtonText'].'",
            closeOnConfirm: '.$a['confirmation_closeOnConfirm'].', }, 
            function(){  location.href="'.$a['url'].'"});        

        ';
    }

    $label = $a['label'];
    $title = ($a['title']) ?: $a['label'];
    $icon = $a['icon'];
    $color = $a['color'] ?: 'primary';
    $confirmation = $a['confirmation'];
    $target = $a['target'] ?: '_self';
    $extra=$a['extra'];
    $url = $a['url'];
    if (isset($confirmation) && ! empty($confirmation)) {
        $url = "javascript:;";
    }
    if(isset($extra){
        foreach ($row as $key => $val) {
            $extra= str_replace("[".$key."]", '"'.$val.'"', $extra);
        }
    }
    if (isset($a['showIf'])) {

        $query = $a['showIf'];

        foreach ($row as $key => $val) {
            $query = str_replace("[".$key."]", '"'.$val.'"', $query);
        }

        @eval("if($query) {
          echo \"<a class='btn btn-xs btn-\$color' title='\$title' onclick='\$confirm_box' href='\$url' target='\$target' \$extra><i class='\$icon'></i> $label</a>&nbsp;\";
      }");
    } else {
        echo "<a class='btn btn-xs btn-$color' title='$title' onclick='$confirm_box' href='$url' target='$target' $extra><i class='$icon'></i> $label</a>&nbsp;";
    }
    ?>
@endforeach

@if($button_action_style == 'button_text')

    @if(CRUDBooster::isRead() && $button_detail)
        <a class='btn btn-xs btn-primary btn-detail' title='{{cbLang("action_detail_data")}}'
           href='{{CRUDBooster::mainpath("detail/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())}}'>{{cbLang("action_detail_data")}}</a>
    @endif

    @if(CRUDBooster::isUpdate() && $button_edit)
        <a class='btn btn-xs btn-success btn-edit' title='{{cbLang("action_edit_data")}}'
           href='{{CRUDBooster::mainpath("edit/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())."&parent_id=".g("parent_id")."&parent_field=".$parent_field }}'>{{cbLang("action_edit_data")}}</a>
    @endif

    @if(CRUDBooster::isDelete() && $button_delete)
        <?php $url = CRUDBooster::mainpath("delete/".$row->$pk);?>
        <a class='btn btn-xs btn-warning btn-delete' title='{{cbLang("action_delete_data")}}' href='javascript:;'
           onclick='{{CRUDBooster::deleteConfirm($url)}}'>{{cbLang("action_delete_data")}}</a>
    @endif
@elseif($button_action_style == 'button_icon_text')


    @if(CRUDBooster::isRead() && $button_detail)
        <a class='btn btn-xs btn-primary btn-detail' title='{{cbLang("action_detail_data")}}'
           href='{{CRUDBooster::mainpath("detail/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())}}'><i
                    class='fa fa-eye'></i> {{cbLang("action_detail_data")}}</a>
    @endif

    @if(CRUDBooster::isUpdate() && $button_edit)
        <a class='btn btn-xs btn-success btn-edit' title='{{cbLang("action_edit_data")}}'
           href='{{CRUDBooster::mainpath("edit/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())."&parent_id=".g("parent_id")."&parent_field=".$parent_field }}'><i
                    class='fa fa-pencil'></i> {{cbLang("action_edit_data")}}</a>
    @endif

    @if(CRUDBooster::isDelete() && $button_delete)
        <?php $url = CRUDBooster::mainpath("delete/".$row->$pk);?>
        <a class='btn btn-xs btn-warning btn-delete' title='{{cbLang("action_delete_data")}}' href='javascript:;'
           onclick='{{CRUDBooster::deleteConfirm($url)}}'><i class='fa fa-trash'></i> {{cbLang("action_delete_data")}}</a>
    @endif

@elseif($button_action_style == 'dropdown')

    <div class='btn-group btn-group-action'>
        <button type='button' class='btn btn-xs btn-primary btn-action'>{{cbLang("action_label")}}</button>
        <button type='button' class='btn btn-xs btn-primary dropdown-toggle' data-toggle='dropdown'>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
        </button>
        <ul class='dropdown-menu dropdown-menu-action' role='menu'>
            @foreach($addaction as $a)
                <?php
                foreach ($row as $key => $val) {
                    $a['url'] = str_replace("[".$key."]", $val, $a['url']);
                }

                $label = $a['label'];
                $url = $a['url']."?return_url=".urlencode(Request::fullUrl());
                $icon = $a['icon'];
                $color = $a['color'] ?: 'primary';

                if (isset($a['showIf'])) {

                    $query = $a['showIf'];

                    foreach ($row as $key => $val) {
                        $query = str_replace("[".$key."]", '"'.$val.'"', $query);
                    }

                    @eval("if($query) {
                        echo \"<li><a title='\$label' href='\$url'><i class='\$icon'></i> \$label</a></li>\";
                    }");
                } else {
                    echo "<li><a title='$label' href='$url'><i class='$icon'></i> $label</a></li>";
                }
                ?>
            @endforeach

            @if(CRUDBooster::isRead() && $button_detail)
                <li><a class='btn-detail' title='{{cbLang("action_detail_data")}}'
                       href='{{CRUDBooster::mainpath("detail/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())}}'><i
                                class='fa fa-eye'></i> {{cbLang("action_detail_data")}}</a></li>
            @endif

            @if(CRUDBooster::isUpdate() && $button_edit)
                <li><a class='btn-edit' title='{{cbLang("action_edit_data")}}'
                       href='{{CRUDBooster::mainpath("edit/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())."&parent_id=".g("parent_id")."&parent_field=".$parent_field}}'><i
                                class='fa fa-pencil'></i> {{cbLang("action_edit_data")}}</a></li>
            @endif

            @if(CRUDBooster::isDelete() && $button_delete)
                <?php $url = CRUDBooster::mainpath("delete/".$row->$pk);?>
                <li><a class='btn-delete' title='{{cbLang("action_delete_data")}}' href='javascript:;'
                       onclick='{{CRUDBooster::deleteConfirm($url)}}'><i class='fa fa-trash'></i> {{cbLang("action_delete_data")}}</a></li>
            @endif
        </ul>
    </div>

@else

    @if(CRUDBooster::isRead() && $button_detail)
        <a class='btn btn-xs btn-primary btn-detail' title='{{cbLang("action_detail_data")}}'
           href='{{CRUDBooster::mainpath("detail/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())}}'><i class='fa fa-eye'></i></a>
    @endif

    @if(CRUDBooster::isUpdate() && $button_edit)
        <a class='btn btn-xs btn-success btn-edit' title='{{cbLang("action_edit_data")}}'
           href='{{CRUDBooster::mainpath("edit/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())."&parent_id=".g("parent_id")."&parent_field=".$parent_field}}'><i
                    class='fa fa-pencil'></i></a>
    @endif

    @if(CRUDBooster::isDelete() && $button_delete)
        <?php $url = CRUDBooster::mainpath("delete/".$row->$pk);?>
        <a class='btn btn-xs btn-warning btn-delete' title='{{cbLang("action_delete_data")}}' href='javascript:;'
           onclick='{{CRUDBooster::deleteConfirm($url)}}'><i class='fa fa-trash'></i></a>
    @endif

@endif
