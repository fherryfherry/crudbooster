<?php
$name = str_slug($form['label'], '');
?>
@push('bottom')
    <script type="text/javascript">
        $(function () {
            $('#form-group-{{$name}} .select2').select2();
        })
    </script>
@endpush
<div class='form-group {{$header_group_class}}' id='form-group-{{$name}}'>

    @if($form['columns'])
        <div class="col-sm-12">

            <div id='panel-form-{{$name}}' class="panel panel-default">
                <div class="panel-heading">
                    <i class='fa fa-bars'></i> {{$form['label']}}
                </div>
                <div class="panel-body">

                    <div class='row'>
                        <div class='col-sm-10'>
                            <div class="panel panel-default">
                                <div class="panel-heading"><i class="fa fa-pencil-square-o"></i> {{cbLang("text_form")}}</div>
                                <div class="panel-body child-form-area">
                                    @foreach($form['columns'] as $col)
                                        <?php $name_column = $name.$col['name'];?>
                                        <div class='form-group'>
                                            @if($col['type']!='hidden')
                                                <label class="control-label col-sm-2">{{$col['label']}}
                                                    @if(!empty($col['required'])) <span class="text-danger"
                                                                                        title="{{cbLang('this_field_is_required')}}">*</span> @endif
                                                </label>
                                            @endif
                                            <div class="col-sm-10">
                                                @if($col['type']=='text')
                                                    <input id='{{$name_column}}' type='text'
                                                           {{ ($col['max'])?"maxlength='".$col['max']."'":"" }} name='child-{{$col["name"]}}'
                                                           class='form-control {{$col['required']?"required":""}}'
                                                            {{($col['readonly']===true)?"readonly":""}}
                                                    />
                                                @elseif($col['type']=='radio')
                                                    <?php
                                                    if($col['dataenum']):
                                                    $dataenum = $col['dataenum'];
                                                    if (strpos($dataenum, ';') !== false) {
                                                        $dataenum = explode(";", $dataenum);
                                                    } else {
                                                        $dataenum = [$dataenum];
                                                    }
                                                    array_walk($dataenum, 'trim');
                                                    foreach($dataenum as $e=>$enum):
                                                    $enum = explode('|', $enum);
                                                    if (count($enum) == 2) {
                                                        $radio_value = $enum[0];
                                                        $radio_label = $enum[1];
                                                    } else {
                                                        $radio_value = $radio_label = $enum[0];
                                                    }
                                                    ?>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="child-{{$col['name']}}"
                                                               class='{{ ($e==0 && $col['required'])?"required":""}} {{$name_column}}'
                                                               value="{{$radio_value}}"{{ ($e==0 && $col['required'])?" checked":""}}> {{$radio_label}}
                                                    </label>
                                                    <?php endforeach;?>
                                                    <?php endif;?>
                                                @elseif($col['type']=='datamodal')

                                                    <div id='{{$name_column}}' class="input-group">
                                                        <input type="hidden" class="input-id">
                                                        <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
                                                        <span class="input-group-btn">
								        <button class="btn btn-primary" onclick="showModal{{$name_column}}()" type="button"><i
                                                    class='fa fa-search'></i> {{cbLang('datamodal_browse_data')}}</button>
								      </span>
                                                    </div><!-- /input-group -->

                                                    @push('bottom')
                                                        <script type="text/javascript">
                                                            var url_{{$name_column}} = "{{CRUDBooster::mainpath('modal-data')}}?table={{$col['datamodal_table']}}&columns=id,{{$col['datamodal_columns']}}&name_column={{$name_column}}&where={{urlencode($col['datamodal_where'])}}&select_to={{ urlencode($col['datamodal_select_to']) }}&columns_name_alias={{urlencode($col['datamodal_columns_alias'])}}&paginate={{urlencode($col['datamodal_paginate'])}}";
                                                            var url_is_setted_{{$name_column}} = false;

                                                            function showModal{{$name_column}}() {
                                                                if (url_is_setted_{{$name_column}} == false) {
                                                                    url_is_setted_{{$name_column}} = true;
                                                                    $('#iframe-modal-{{$name_column}}').attr('src', url_{{$name_column}});
                                                                }
                                                                $('#modal-datamodal-{{$name_column}}').modal('show');
                                                            }

                                                            function hideModal{{$name_column}}() {
                                                                $('#modal-datamodal-{{$name_column}}').modal('hide');
                                                            }

                                                            function selectAdditionalData{{$name_column}}(select_to_json) {
                                                                $.each(select_to_json, function (key, val) {
                                                                    console.log('#' + key + ' = ' + val);
                                                                    if (key == 'datamodal_id') {
                                                                        $('#{{$name_column}} .input-id').val(val);
                                                                    }
                                                                    if (key == 'datamodal_label') {
                                                                        $('#{{$name_column}} .input-label').val(val);
                                                                    }
                                                                    $('#{{$name}}' + key).val(val).trigger('change');
                                                                })
                                                                hideModal{{$name_column}}();
                                                            }
                                                        </script>
                                                    @endpush

                                                    <div id='modal-datamodal-{{$name_column}}' class="modal" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog {{ $col['datamodal_size']=='large'?'modal-lg':'' }} " role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                                aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title"><i
                                                                                class='fa fa-search'></i> {{cbLang('datamodal_browse_data')}} {{$col['label']}}
                                                                    </h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <iframe id='iframe-modal-{{$name_column}}' style="border:0;height:{{$col['datamodal_height']?: "430px"}};width: 100%"
                                                                            src=""></iframe>
                                                                </div>

                                                            </div><!-- /.modal-content -->
                                                        </div><!-- /.modal-dialog -->
                                                    </div><!-- /.modal -->

                                                @elseif($col['type']=='number')
                                                    <input id='{{$name_column}}' type='number'
                                                           {{ ($col['min'])?"min='".$col['min']."'":"" }} {{ ($col['max'])?"max='$col[max]'":"" }} name='child-{{$col["name"]}}'
                                                           class='form-control {{$col['required']?"required":""}}'
                                                            {{($col['readonly']===true)?"readonly":""}}
                                                    />
                                                @elseif($col['type']=='textarea')
                                                    <textarea id='{{$name_column}}' name='child-{{$col["name"]}}'
                                                              class='form-control {{$col['required']?"required":""}}' {{($col['readonly']===true)?"readonly":""}} ></textarea>
                                                @elseif($col['type']=='upload')
                                                    <div id='{{$name_column}}' class="input-group">
                                                        <input type="hidden" class="input-id">
                                                        <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
                                                        <span class="input-group-btn">
								        <button class="btn btn-primary" id="btn-upload-{{$name_column}}" onclick="showFakeUpload{{$name_column}}()"
                                                type="button"><i class='fa fa-search'></i> {{cbLang('datamodal_browse_file')}}</button>
								      </span>
                                                    </div><!-- /input-group -->

                                                    <div id="loading-{{$name_column}}" class='text-info' style="display: none">
                                                        <i class='fa fa-spin fa-spinner'></i> {{cbLang('text_loading')}}
                                                    </div>

                                                    <input type="file" id='fake-upload-{{$name_column}}' style="display: none">
                                                    @push('bottom')
                                                        <script type="text/javascript">
                                                            var file;
                                                            var filename;
                                                            var is_uploading = false;

                                                            function showFakeUpload{{$name_column}}() {
                                                                if (is_uploading) {
                                                                    return false;
                                                                }

                                                                $('#fake-upload-{{$name_column}}').click();
                                                            }

                                                            // Add events
                                                            $('#fake-upload-{{$name_column}}').on('change', prepareUpload{{$name_column}});

                                                            // Grab the files and set them to our variable
                                                            function prepareUpload{{$name_column}}(event) {
                                                                var max_size = {{ ($col['max'])?:2000 }};
                                                                file = event.target.files[0];

                                                                var filesize = Math.round(parseInt(file.size) / 1024);

                                                                if (filesize > max_size) {
                                                                    sweetAlert('{{cbLang("alert_warning")}}', '{{cbLang("your_file_size_is_too_big")}}', 'warning');
                                                                    return false;
                                                                }

                                                                filename = $('#fake-upload-{{$name_column}}').val().replace(/C:\\fakepath\\/i, '');
                                                                var extension = filename.split('.').pop().toLowerCase();
                                                                var img_extension = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                                                                var available_extension = "{{config('crudbooster.UPLOAD_TYPES')}}".split(",");
                                                                var is_image_only = {{ ($col['upload_type'] == 'image')?"true":"false" }};

                                                                if (is_image_only) {
                                                                    if ($.inArray(extension, img_extension) == -1) {
                                                                        sweetAlert('{{cbLang("alert_warning")}}', '{{cbLang("your_file_extension_is_not_allowed")}}', 'warning');
                                                                        return false;
                                                                    }
                                                                } else {
                                                                    if ($.inArray(extension, available_extension) == -1) {
                                                                        sweetAlert('{{cbLang("alert_warning")}}', '{{cbLang("your_file_extension_is_not_allowed")}}!', 'warning');
                                                                        return false;
                                                                    }
                                                                }


                                                                $('#{{$name_column}} .input-label').val(filename);

                                                                $('#loading-{{$name_column}}').fadeIn();
                                                                $('#btn-add-table-{{$name}}').addClass('disabled');
                                                                $('#btn-upload-{{$name_column}}').addClass('disabled');
                                                                is_uploading = true;

                                                                //Upload File To Server
                                                                uploadFiles{{$name_column}}(event);
                                                            }

                                                            function uploadFiles{{$name_column}}(event) {
                                                                event.stopPropagation(); // Stop stuff happening
                                                                event.preventDefault(); // Totally stop stuff happening

                                                                // START A LOADING SPINNER HERE

                                                                // Create a formdata object and add the files
                                                                var data = new FormData();
                                                                data.append('userfile', file);

                                                                $.ajax({
                                                                    url: '{{CRUDBooster::mainpath("upload-file")}}',
                                                                    type: 'POST',
                                                                    data: data,
                                                                    cache: false,
                                                                    processData: false, // Don't process the files
                                                                    contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                                                                    success: function (data, textStatus, jqXHR) {
                                                                        console.log(data);
                                                                        $('#btn-add-table-{{$name}}').removeClass('disabled');
                                                                        $('#loading-{{$name_column}}').hide();
                                                                        $('#btn-upload-{{$name_column}}').removeClass('disabled');
                                                                        is_uploading = false;

                                                                        var basename = data.split('/').reverse()[0];
                                                                        $('#{{$name_column}} .input-label').val(basename);

                                                                        $('#{{$name_column}} .input-id').val(data);
                                                                    },
                                                                    error: function (jqXHR, textStatus, errorThrown) {
                                                                        $('#btn-add-table-{{$name}}').removeClass('disabled');
                                                                        $('#btn-upload-{{$name_column}}').removeClass('disabled');
                                                                        is_uploading = false;
                                                                        // Handle errors here
                                                                        console.log('ERRORS: ' + textStatus);
                                                                        // STOP LOADING SPINNER
                                                                        $('#loading-{{$name_column}}').hide();
                                                                    }
                                                                });
                                                            }

                                                        </script>
                                                    @endpush

                                                @elseif($col['type']=='select')

                                                    @if($col['parent_select'])
                                                        @push('bottom')
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $("#{{$name.$col['parent_select']}} , #{{$name.$col['name']}}").select2("destroy");

                                                                    $('#{{$name.$col['parent_select']}}, input:radio[name={{$name.$col['parent_select']}}]').change(function () {
                                                                        var $current = $("#{{$name.$col['name']}}");
                                                                        var parent_id = $(this).val();
                                                                        var fk_name = "{{$col['parent_select']}}";
                                                                        var fk_value = $('#{{$name.$col['parent_select']}}').val();
                                                                        var datatable = "{{$col['datatable']}}".split(',');
                                                                        var datatableWhere = "{{$col['datatable_where']}}";
                                                                        var table = datatable[0].trim('');
                                                                        var label = datatable[1].trim('');
                                                                        var value = "{{$value}}";

                                                                        if (fk_value != '') {
                                                                            $current.html("<option value=''>{{cbLang('text_loading')}} {{$col['label']}}");
                                                                            $.get("{{CRUDBooster::mainpath('data-table')}}?table=" + table + "&label=" + label + "&fk_name=" + fk_name + "&fk_value=" + fk_value + "&datatable_where=" + encodeURI(datatableWhere), function (response) {
                                                                                if (response) {
                                                                                    $current.html("<option value=''>{{$default}}");
                                                                                    $.each(response, function (i, obj) {
                                                                                        var selected = (value && value == obj.select_value) ? "selected" : "";
                                                                                        $("<option " + selected + " value='" + obj.select_value + "'>" + obj.select_label + "</option>").appendTo("#{{$name.$col['name']}}");
                                                                                    });
                                                                                    $current.trigger('change');
                                                                                }
                                                                            });
                                                                        } else {
                                                                            $current.html("<option value=''>{{$default}}");
                                                                        }
                                                                    });

                                                                    $('#{{$name.$col['parent_select']}}').trigger('change');
                                                                    $("#{{$name.$col['name']}}").trigger('change');

                                                                    $("#{{$name.$col['parent_select']}} , #{{$name.$col['name']}}").select2();

                                                                })
                                                            </script>
                                                        @endpush
                                                    @endif

                                                    <select id='{{$name_column}}' name='child-{{$col["name"]}}'
                                                            class='form-control select2 {{$col['required']?"required":""}}'
                                                            {{($col['readonly']===true)?"readonly":""}}
                                                    >
                                                        <option value=''>{{cbLang('text_prefix_option')}} {{$col['label']}}</option>
                                                        <?php
                                                        if ($col['datatable']) {
                                                            $tableJoin = explode(',', $col['datatable'])[0];
                                                            $titleField = explode(',', $col['datatable'])[1];
                                                            if (! $col['datatable_where']) {
                                                                $data = CRUDBooster::get($tableJoin, NULL, "$titleField ASC");
                                                            } else {
                                                                $data = CRUDBooster::get($tableJoin, $col['datatable_where'], "$titleField ASC");
                                                            }
                                                            foreach ($data as $d) {
                                                                echo "<option value='$d->id'>".$d->$titleField."</option>";
                                                            }
                                                        } else {
                                                            $data = $col['dataenum'];
                                                            foreach ($data as $d) {
                                                                $enum = explode('|', $d);
                                                                if (count($enum) == 2) {
                                                                    $opt_value = $enum[0];
                                                                    $opt_label = $enum[1];
                                                                } else {
                                                                    $opt_value = $opt_label = $enum[0];
                                                                }
                                                                echo "<option value='$opt_value'>$opt_label</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                @elseif($col['type']=='hidden')
                                                    <input type="{{$col['type']}}" id="{{$name.$col["name"]}}" name="child-{{$name.$col["name"]}}"
                                                           value="{{$col["value"]}}">
                                                @endif

                                                @if($col['help'])
                                                    <div class='help-block'>
                                                        {{$col['help']}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if($col['formula'])
                                            <?php
                                            $formula = $col['formula'];
                                            $formula_function_name = 'formula'.str_slug($name.$col['name'], '');
                                            $script_onchange = "";
                                            foreach ($form['columns'] as $c) {
                                                if (strpos($formula, "[".$c['name']."]") !== false) {
                                                    $script_onchange .= "
											$('#$name$c[name]').change(function() {
												$formula_function_name();
											});
											";
                                                }
                                                $formula = str_replace("[".$c['name']."]", "\$('#".$name.$c['name']."').val()", $formula);
                                            }
                                            ?>
                                            @push('bottom')
                                                <script type="text/javascript">
                                                    function {{ $formula_function_name }}() {
                                                        var v = {!! $formula !!};
                                                        $('#{{$name_column}}').val(v);
                                                    }

                                                    $(function () {
                                                        {!! $script_onchange !!}
                                                    })
                                                </script>
                                            @endpush
                                        @endif

                                    @endforeach

                                    @push('bottom')
                                        <script type="text/javascript">
                                            var currentRow = null;

                                            function resetForm{{$name}}() {
                                                $('#panel-form-{{$name}}').find("input[type=text],input[type=number],select,textarea").val('');
                                                $('#panel-form-{{$name}}').find(".select2").val('').trigger('change');
                                            }

                                            function deleteRow{{$name}}(t) {

                                                if (confirm("{{cbLang('delete_title_confirm')}}")) {
                                                    $(t).parent().parent().remove();
                                                    if ($('#table-{{$name}} tbody tr').length == 0) {
                                                        var colspan = $('#table-{{$name}} thead tr th').length;
                                                        $('#table-{{$name}} tbody').html("<tr class='trNull'><td colspan='" + colspan + "' align='center'>{{cbLang('table_data_not_found')}}</td></tr>");
                                                    }
                                                }
                                            }

                                            function editRow{{$name}}(t) {
                                                var p = $(t).parent().parent(); //parentTR
                                                currentRow = p;
                                                p.addClass('warning');
                                                $('#btn-add-table-{{$name}}').val('{{cbLang("save_changes")}}');
                                                @foreach($form['columns'] as $c)
                                                @if($c['type']=='select')
                                                $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val()).trigger("change");
                                                        @elseif($c['type']=='radio')
                                                var v = p.find(".{{$c['name']}} input").val();
                                                $('.{{$name.$c["name"]}}[value=' + v + ']').prop('checked', true);
                                                @elseif($c['type']=='datamodal')
                                                $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} .td-label").text());
                                                $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
                                                @elseif($c['type']=='upload')
                                                @if($c['upload_type']=='image')
                                                $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} img").data('label'));
                                                @else
                                                $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} a").data('label'));
                                                @endif
                                                $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
                                                @else
                                                $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val());
                                                @endif
                                                @endforeach
                                            }

                                            function validateForm{{$name}}() {
                                                var is_false = 0;
                                                $('#panel-form-{{$name}} .required').each(function () {
                                                    var v = $(this).val();
                                                    if (v == '') {
                                                        sweetAlert("{{cbLang('alert_warning')}}", "{{cbLang('please_complete_the_form')}}", "warning");
                                                        is_false += 1;
                                                    }
                                                })

                                                if (is_false == 0) {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            }

                                            function addToTable{{$name}}() {

                                                if (validateForm{{$name}}() == false) {
                                                    return false;
                                                }

                                                var trRow = '<tr>';
                                                @foreach($form['columns'] as $c)
                                                        @if($c['type']=='select')
                                                    trRow += "<td class='{{$c['name']}}'>" + $('#{{$name.$c["name"]}} option:selected').text() +
                                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                                                    "</td>";
                                                @elseif($c['type']=='radio')
                                                    trRow += "<td class='{{$c['name']}}'><span class='td-label'>" + $('.{{$name.$c["name"]}}:checked').val() + "</span>" +
                                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('.{{$name.$c["name"]}}:checked').val() + "'/>" +
                                                    "</td>";
                                                @elseif($c['type']=='datamodal')
                                                    trRow += "<td class='{{$c['name']}}'><span class='td-label'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</span>" +
                                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                                    "</td>";
                                                @elseif($c['type']=='upload')
                                                        @if($c['upload_type']=='image')
                                                            trRow += "<td class='{{$c['name']}}'>" +
                                                            "<a data-lightbox='roadtrip' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'><img data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' src='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "' width='50px' height='50px'/></a>" +
                                                            "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                                            "</td>";
                                                        @else
                                                            trRow += "<td class='{{$c['name']}}'><a data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</a>" +
                                                            "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                                            "</td>";
                                                        @endif
                                                @elseif($c['type']=='hidden')
                                                    trRow += "<td class='{{$c['name']}}' style='display:none;'><input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/></td>";
                                                @else
                                                    trRow += "<td class='{{$c['name']}}'>" + $('#{{$name.$c["name"]}}').val() +
                                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                                                    "</td>";
                                                @endif
                                                        @endforeach
                                                    trRow += "<td>" +
                                                    "<a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this)' class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></a> " +
                                                    "<a href='javascript:void(0)' onclick='deleteRow{{$name}}(this)' class='btn btn-danger btn-xs'><i class='fa fa-trash'></i></a></td>";
                                                trRow += '</tr>';
                                                $('#table-{{$name}} tbody .trNull').remove();
                                                if (currentRow == null) {
                                                    $("#table-{{$name}} tbody").prepend(trRow);
                                                } else {
                                                    currentRow.removeClass('warning');
                                                    currentRow.replaceWith(trRow);
                                                    currentRow = null;
                                                }
                                                $('#btn-add-table-{{$name}}').val('{{cbLang("button_add_to_table")}}');
                                                $('#btn-reset-form-{{$name}}').click();
                                            }
                                        </script>
                                    @endpush
                                </div>
                                <div class="panel-footer" align="right">
                                    <input type='button' class='btn btn-default' id="btn-reset-form-{{$name}}" onclick="resetForm{{$name}}()"
                                           value='{{cbLang("button_reset")}}'/>
                                    <input type='button' id='btn-add-table-{{$name}}' class='btn btn-primary' onclick="addToTable{{$name}}()"
                                           value='{{cbLang("button_add_to_table")}}'/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class='fa fa-table'></i> {{cbLang('table_detail')}}
                        </div>
                        <div class="panel-body no-padding table-responsive" style="max-height: 400px;overflow: auto;">
                            <table id='table-{{$name}}' class='table table-striped table-bordered'>
                                <thead>
                                <tr>
                                    @foreach($form['columns'] as $col)
                                        @if($col['type']!='hidden')
                                            <th>{{$col['label']}}</th>
                                        @else
                                            <th style="display:none;">{{$col['label']}}</th>
                                        @endif
                                    @endforeach
                                    <th width="90px">{{cbLang('action_label')}}</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                $columns_tbody = [];
                                $data_child = DB::table($form['table'])->where($form['foreign_key'], $id);
                                foreach ($form['columns'] as $i => $c) {
                                    $data_child->addselect($form['table'].'.'.$c['name']);

                                    if ($c['type'] == 'datamodal') {
                                        $datamodal_title = explode(',', $c['datamodal_columns'])[0];
                                        $datamodal_table = $c['datamodal_table'];
                                        $data_child->join($c['datamodal_table'], $c['datamodal_table'].'.id', '=', $c['name']);
                                        $data_child->addselect($c['datamodal_table'].'.'.$datamodal_title.' as '.$datamodal_table.'_'.$datamodal_title);
                                    } elseif ($c['type'] == 'select') {
                                        if ($c['datatable']) {
                                            $join_table = explode(',', $c['datatable'])[0];
                                            $join_field = explode(',', $c['datatable'])[1];
                                            $data_child->join($join_table, $join_table.'.id', '=', $c['name']);
                                            $data_child->addselect($join_table.'.'.$join_field.' as '.$join_table.'_'.$join_field);
                                        }
                                    }
                                }

                                $data_child = $data_child->orderby($form['table'].'.id', 'desc')->get();
                                foreach($data_child as $d):
                                ?>
                                <tr>
                                    @foreach($form['columns'] as $col)
                                            <?php
                                            if ($col['type'] == 'select') {
                                                echo "<td class='".$col['name']."'>";
                                                if ($col['datatable']) {
                                                    $join_table = explode(',', $col['datatable'])[0];
                                                    $join_field = explode(',', $col['datatable'])[1];
                                                    echo "<span class='td-label'>";
                                                    echo $d->{$join_table.'_'.$join_field};
                                                    echo "</span>";
                                                    echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                                }
                                                if ($col['dataenum']) {
                                                    echo "<span class='td-label'>";
                                                    echo $d->{$col['name']};
                                                    echo "</span>";
                                                    echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                                }
                                                echo "</td>";
                                            } elseif ($col['type'] == 'datamodal') {
                                                echo "<td class='".$col['name']."'>";
                                                $datamodal_title = explode(',', $col['datamodal_columns'])[0];
                                                $datamodal_table = $col['datamodal_table'];
                                                echo "<span class='td-label'>";
                                                echo $d->{$datamodal_table.'_'.$datamodal_title};
                                                echo "</span>";
                                                echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                                echo "</td>";
                                            } elseif ($col['type'] == 'upload') {
                                                echo "<td class='".$col['name']."'>";
                                                $filename = basename($d->{$col['name']});
                                                if ($col['upload_type'] == 'image') {
                                                    echo "<a href='".asset($d->{$col['name']})."' data-lightbox='roadtrip'><img data-label='$filename' src='".asset($d->{$col['name']})."' width='50px' height='50px'/></a>";
                                                    echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                                } else {
                                                    echo "<a data-label='$filename' href='".asset($d->{$col['name']})."'>$filename</a>";
                                                    echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                                }
                                                echo "</td>";
                                            } elseif ($col['type'] == 'hidden') {
                                                echo "<td class='".$col['name']."'style='display:none;'>";
                                                echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                                echo "</td>";
                                            } else {
                                                echo "<td class='".$col['name']."'>";
                                                echo "<span class='td-label'>";
                                                echo $d->{$col['name']};
                                                echo "</span>";
                                                echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                                echo "</td>";
                                            }
                                            ?>
                                    @endforeach
                                    <td>
                                        <a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this)' class='btn btn-warning btn-xs'><i
                                                    class='fa fa-pencil'></i></a>
                                        <a href='javascript:void(0)' onclick='deleteRow{{$name}}(this)' class='btn btn-danger btn-xs'><i
                                                    class='fa fa-trash'></i></a>
                                    </td>
                                </tr>

                                <?php endforeach;?>

                                @if(count($data_child)==0)
                                    <tr class="trNull">
                                        <td colspan="{{count($form['columns'])+1}}" align="center">{{cbLang('table_data_not_found')}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>


        </div>


    @else

        <div style="border:1px dashed #c41300;padding:20px;margin:20px">
            <span style="background: yellow;color: black;font-weight: bold">CHILD {{$name}} : COLUMNS ATTRIBUTE IS MISSING !</span>
            <p>You need to set the "columns" attribute manually</p>
        </div>
    @endif
</div>
