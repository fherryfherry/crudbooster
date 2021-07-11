@extends('crudbooster::admin_template')

@section('content')

    @if($index_statistic)
        <div id='box-statistic' class='row'>
            @foreach($index_statistic as $stat)
                <div class="{{ ($stat['width'])?:'col-sm-3' }}">
                    <div class="small-box bg-{{ $stat['color']?:'red' }}">
                        <div class="inner">
                            <h3>{{ $stat['count'] }}</h3>
                            <p>{{ $stat['label'] }}</p>
                        </div>
                        <div class="icon">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(!is_null($pre_index_html) && !empty($pre_index_html))
        {!! $pre_index_html !!}
    @endif


    @if(g('return_url'))
        <p><a href='{{g("return_url")}}'><i class='fa fa-chevron-circle-{{ cbLang('left') }}'></i>
                &nbsp; {{cbLang('form_back_to_list',['module'=>urldecode(g('label'))])}}</a></p>
    @endif

    @if($parent_table)
        <div class="box box-default">
            <div class="box-body table-responsive no-padding">
                <table class='table table-bordered'>
                    <tbody>
                    <tr class='active'>
                        <td colspan="2"><strong><i class='fa fa-bars'></i> {{ ucwords(urldecode(g('label'))) }}</strong></td>
                    </tr>
                    @foreach(explode(',',urldecode(g('parent_columns'))) as $c)
                        <tr>
                            <td width="25%"><strong>
                                    @if(urldecode(g('parent_columns_alias')))
                                        {{explode(',',urldecode(g('parent_columns_alias')))[$loop->index]}}
                                    @else
                                        {{  ucwords(str_replace('_',' ',$c)) }}
                                    @endif
                                </strong></td>
                            <td> {{ $parent_table->$c }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="box">
        <div class="box-header">
            <div class="container-fluid" style="padding:0;">
                <div class="row">
                    <?php $grid_col = 12; ?>
                    @if($button_bulk_action && ( ($button_delete && CRUDBooster::isDelete()) || $button_selected) )
                        <?php $grid_col = $grid_col - 2; ?>
                        <div class="col-sm-2">
                            <div class="box-tools pull-{{ cbLang('left') }}">
                                <div class="selected-action" style="display:inline-block;position:relative;">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false"><i
                                            class='fa fa-check-square-o'></i> {{cbLang("button_selected_action")}}
                                        <span class="fa fa-caret-down"></span></button>
                                    <ul class="dropdown-menu">
                                        @if($button_delete && CRUDBooster::isDelete())
                                            <li><a href="javascript:void(0)" data-name='delete'
                                                   title='{{cbLang('action_delete_selected')}}'><i
                                                        class="fa fa-trash"></i> {{cbLang('action_delete_selected')}}
                                                </a></li>
                                        @endif

                                        @if($button_selected)
                                            @foreach($button_selected as $button)
                                                <li><a href="javascript:void(0)" data-name='{{$button["name"]}}'
                                                       title='{{$button["label"]}}'><i
                                                            class="fa fa-{{$button['icon']}}"></i> {{$button['label']}}
                                                    </a></li>
                                            @endforeach
                                        @endif

                                    </ul><!--end-dropdown-menu-->
                                </div><!--end-selected-action-->
                            </div><!--end-pull-left-->
                        </div><!--end-col-sm-2-->
                    @endif
                    @if($button_date_filter)
                        <?php $grid_col = $grid_col - 5; ?>
                        <div class="col-sm-5">
                            <div class="box-tools pull-{{ cbLang('left') }}">
                                <div class="row">
                                    <form method='get' action='{{Request::url()}}'>
                                        <input type="hidden" name="filter_column[{{$table}}.created_at][type]"
                                               value="between">
                                        <input type="hidden" name="last_url" value={{Request::fullurl()}}>
                                        <?php
                                        $filter = Request::get( 'filter_column' );
                                        $key = $table . '.created_at';
                                        $value = $filter[$key]['value'];
                                        ?>
                                        <div class="col-sm-5 col-xs-5">
                                            <div class="input-group">

                                <span class="input-group-addon">
                                    <a href="javascript:void(0)"
                                       onclick="$('#start_date').data('daterangepicker').toggle();">
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                </span>
                                                <input name="filter_column[{{$table}}.created_at][value][]" type="text"
                                                       placeholder="{{ cbLang('crudbooster.filter_date_start') }}"
                                                       title="{{ cbLang('crudbooster.filter_date_start') }}" readonly=""
                                                       required="" class="form-control @if($filter[$key]) @else datetimepicker @endif" id="start_date"
                                                       value="{{$value[0]}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-5 col-xs-5">
                                            <div class="input-group ">

                                <span class="input-group-addon">
                                    <a href="javascript:void(0)"
                                       onclick="$('#end_date').data('daterangepicker').toggle();">
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                </span>
                                                <input name="filter_column[{{$table}}.created_at][value][]" type="text"
                                                       placeholder="{{ cbLang('crudbooster.filter_date_end') }}"
                                                       title="{{ cbLang('crudbooster.filter_date_end') }}" readonly=""
                                                       required="" class="form-control @if($filter[$key]) @else datetimepicker @endif" id="end_date"
                                                       value="{{$value[1]}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-2">
                                            @if(!g("last_url"))
                                                <button type='submit' class="btn btn-sm btn-default"><i
                                                        class="fa fa-search"></i></button>
                                            @else
                                                <a href={{urldecode(g("last_url"))}} class="btn btn-sm btn-danger"><i
                                                    class="fa fa-ban"></i></a>
                                            @endif
                                        </div>
                                    </form>
                                </div><!--end-row-->
                            </div><!--end-pull-left-->
                        </div><!--end-col-sm-5-->
                    @endif
                    <div class="col-sm-{{$grid_col}}">
                        <div class="box-tools pull-{{ cbLang('right') }}" style="position: relative;">
                            <div style="display: inline-flex">

                                @if($button_filter)
                                    <a href="javascript:void(0)" id='btn_advanced_filter'
                                       data-url-parameter='{{$build_query}}'
                                       title='{{cbLang('filter_dialog_title')}}'
                                       class="btn btn-sm btn-default {{(Request::get('filter_column'))?'active':''}}">
                                        <i class="fa fa-filter"></i> {{cbLang("button_filter")}}
                                    </a>
                                @endif

                                <form method='get' action='{{Request::url()}}'>
                                    <div class="input-group">
                                        <input type="text" name="q" value="{{ Request::get('q') }}"
                                               class="form-control input-sm pull-{{ cbLang('right') }}"
                                               placeholder="{{cbLang('filter_search')}}"/>
                                        {!! CRUDBooster::getUrlParameters(['q']) !!}
                                        <div class="input-group-btn">
                                            @if(Request::get('q'))
                                                <?php
                                                $parameters = Request::all();
                                                unset( $parameters['q'] );
                                                $build_query = urldecode( http_build_query( $parameters ) );
                                                $build_query = ( $build_query ) ? "?" . $build_query : "";
                                                $build_query = ( Request::all() ) ? $build_query : "";
                                                ?>
                                                <button type='button'
                                                        onclick='location.href="{{ CRUDBooster::mainpath().$build_query}}"'
                                                        title="{{cbLang('button_reset')}}"
                                                        class='btn btn-sm btn-warning'><i class='fa fa-ban'></i>
                                                </button>
                                            @endif
                                            <button type='submit' class="btn btn-sm btn-default"><i
                                                    class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>


                                <form method='get' id='form-limit-paging' action='{{Request::url()}}'>
                                    {!! CRUDBooster::getUrlParameters(['limit']) !!}
                                    <div class="input-group">
                                        <select onchange="$('#form-limit-paging').submit()" name='limit'
                                                style="width: 56px;" class='form-control input-sm'>
                                            <option {{($limit==5)?'selected':''}} value='5'>5</option>
                                            <option {{($limit==10)?'selected':''}} value='10'>10</option>
                                            <option {{($limit==20)?'selected':''}} value='20'>20</option>
                                            <option {{($limit==25)?'selected':''}} value='25'>25</option>
                                            <option {{($limit==50)?'selected':''}} value='50'>50</option>
                                            <option {{($limit==100)?'selected':''}} value='100'>100</option>
                                            <option {{($limit==200)?'selected':''}} value='200'>200</option>
                                        </select>
                                    </div>
                                </form>

                            </div><!--end-display-inline-flex-->
                        </div><!--end-box-tools-->
                    </div><!--end-col-->
                </div><!--end-row-->
                <br style="clear:both"/>
            </div><!--end-container-->
        </div><!--end-box-header-->
        <div class="box-body table-responsive no-padding">
            @include("crudbooster::default.table")
        </div>
    </div><!--end-box-->

    @if(!is_null($post_index_html) && !empty($post_index_html))
        {!! $post_index_html !!}
    @endif

@endsection
