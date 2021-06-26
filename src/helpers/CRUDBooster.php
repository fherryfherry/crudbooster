<?php

namespace crocodicstudio\crudbooster\helpers;

use Cache;
use DB;
use Image;
use Request;
use Route;
use Schema;
use Session;
use Storage;
use Validator;

class CRUDBooster
{
    /**
     *	Comma-delimited data output from the child table
     */
    public static function echoSelect2Mult($values, $table, $id, $name) {
        $values = explode(",", $values);
        return implode(", ", DB::table($table)->whereIn($id, $values)->pluck($name)->toArray());
        //implode(", ", DB::table("syudo_list_pokemons_types")->whereIn("id", explode(",", $row->type))->pluck("name")->toArray())

    }

    public static function uploadBase64($value, $id = null)
    {
        if (! self::myId()) {
            $userID = 0;
        } else {
            $userID = self::myId();
        }

        if ($id) {
            $userID = $id;
        }

        $filedata = base64_decode($value);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $filedata, FILEINFO_MIME_TYPE);
        @$mime_type = explode('/', $mime_type);
        @$mime_type = $mime_type[1];
        if ($mime_type) {
            $filePath = 'uploads/'.$userID.'/'.date('Y-m');
            Storage::makeDirectory($filePath);
            $filename = md5(str_random(5)).'.'.$mime_type;
            if (Storage::put($filePath.'/'.$filename, $filedata)) {
                self::resizeImage($filePath.'/'.$filename);

                return $filePath.'/'.$filename;
            }
        }
    }

    public static function uploadFile($name, $encrypt = false, $resize_width = null, $resize_height = null, $id = null)
    {
        if (Request::hasFile($name)) {
            if (! self::myId()) {
                $userID = 0;
            } else {
                $userID = self::myId();
            }

            if ($id) {
                $userID = $id;
            }

            $file = Request::file($name);
            $ext = $file->getClientOriginalExtension();
            $filename = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            if(method_exists($file, 'getClientSize')) {
                $filesize = $file->getClientSize() / 1024;
            } else {
                $filesize = $file->getSize() / 1024;
            }
            $file_path = 'uploads/'.$userID.'/'.date('Y-m');

            //Create Directory Monthly
            Storage::makeDirectory($file_path);

            if ($encrypt == true) {
                $filename = md5(str_random(5)).'.'.$ext;
            } else {
                $filename = str_slug($filename, '_').'.'.$ext;
            }

            if (Storage::putFileAs($file_path, $file, $filename)) {
                self::resizeImage($file_path.'/'.$filename, $resize_width, $resize_height);

                return $file_path.'/'.$filename;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    private static function resizeImage($fullFilePath, $resize_width = null, $resize_height = null, $qty = 100, $thumbQty = 75)
    {
        $images_ext = config('crudbooster.IMAGE_EXTENSIONS', 'jpg,png,gif,bmp');
        $images_ext = explode(',', $images_ext);

        $filename = basename($fullFilePath);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $file_path = trim(str_replace($filename, '', $fullFilePath), '/');

        $file_path_thumbnail = 'uploads_thumbnail/'.date('Y-m');
        Storage::makeDirectory($file_path_thumbnail);

        if (in_array(strtolower($ext), $images_ext)) {

            if ($resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->fit($resize_width, $resize_height);
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif ($resize_width && ! $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize($resize_width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif (! $resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize(null, $resize_height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } else {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                if ($img->width() > 1300) {
                    $img->resize(1300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            }

            $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
            $img->fit(350, 350);
            $img->save(storage_path('app/'.$file_path_thumbnail.'/'.$filename), $thumbQty);
        }
    }

    public static function getSetting($name)
    {
        if (Cache::has('setting_'.$name)) {
            return Cache::get('setting_'.$name);
        }

        $query = DB::table('cms_settings')->where('name', $name)->first();
        Cache::forever('setting_'.$name, $query->content);

        return $query->content;
    }

    public static function insert($table, $data = [])
    {
        if (! $data['created_at']) {
            if (Schema::hasColumn($table, 'created_at')) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
        }

        if (DB::table($table)->insert($data)) {
            return $data['id'];
        } else {
            return false;
        }
    }

    public static function first($table, $id)
    {
        $table = self::parseSqlTable($table)['table'];
        if (is_array($id)) {
            $first = DB::table($table);
            foreach ($id as $k => $v) {
                $first->where($k, $v);
            }

            return $first->first();
        } else {
            $pk = self::pk($table);

            return DB::table($table)->where($pk, $id)->first();
        }
    }

    public static function get($table, $string_conditions = null, $orderby = null, $limit = null, $skip = null)
    {
        $table = self::parseSqlTable($table);
        $table = $table['table'];
        $query = DB::table($table);
        if ($string_conditions) {
            $query->whereraw($string_conditions);
        }
        if ($orderby) {
            $query->orderbyraw($orderby);
        }
        if ($limit) {
            $query->take($limit);
        }
        if ($skip) {
            $query->skip($skip);
        }

        return $query->get();
    }

    public static function me()
    {
        return DB::table(config('crudbooster.USER_TABLE'))->where('id', Session::get('admin_id'))->first();
    }

    public static function myId()
    {
        return Session::get('admin_id');
    }

    public static function isSuperadmin()
    {
        return Session::get('admin_is_superadmin');
    }

    public static function myName()
    {
        return Session::get('admin_name');
    }

    public static function myPhoto()
    {
        return Session::get('admin_photo');
    }

    public static function myPrivilege()
    {
        $roles = Session::get('admin_privileges_roles');
        if ($roles) {
            foreach ($roles as $role) {
                if ($role->path == CRUDBooster::getModulePath()) {
                    return $role;
                }
            }
        }
    }

    public static function myPrivilegeId()
    {
        return Session::get('admin_privileges');
    }

    public static function myPrivilegeName()
    {
        return Session::get('admin_privileges_name');
    }

    public static function isLocked()
    {
        return Session::get('admin_lock');
    }

    public static function redirectBack($message, $type = 'warning')
    {

        if (Request::ajax()) {
            $resp = response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $_SERVER['HTTP_REFERER']])->send();
            exit;
        } else {
            $resp = redirect()->back()->with(['message' => $message, 'message_type' => $type]);
            Session::driver()->save();
            $resp->send();
            exit;
        }
    }

    public static function redirect($to, $message, $type = 'warning')
    {

        if (Request::ajax()) {
            $resp = response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $to])->send();
            exit;
        } else {
            $resp = redirect($to)->with(['message' => $message, 'message_type' => $type]);
            Session::driver()->save();
            $resp->send();
            exit;
        }
    }

    public static function isView()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_visible;
            }
        }
    }

    public static function isUpdate()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_edit;
            }
        }
    }

    public static function isCreate()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_create;
            }
        }
    }

    public static function isRead()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_read;
            }
        }
    }

    public static function isDelete()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_delete;
            }
        }
    }

    public static function isCRUD()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                if ($v->is_visible && $v->is_create && $v->is_read && $v->is_edit && $v->is_delete) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public static function getCurrentModule()
    {
        $modulepath = self::getModulePath();

        if (Cache::has('moduls_'.$modulepath)) {
            return Cache::get('moduls_'.$modulepath);
        } else {

            $module = DB::table('cms_moduls')->where('path', self::getModulePath())->first();

            //supply modulpath instead of $module incase where user decides to create form and custom url that does not exist in cms_moduls table.
            return ($module)?:$modulepath;
        }
    }

    public static function getCurrentDashboardId()
    {
        if (Request::get('d') != null) {
            Session::put('currentDashboardId', Request::get('d'));
            Session::put('currentMenuId', 0);

            return Request::get('d');
        } else {
            return Session::get('currentDashboardId');
        }
    }

    public static function getCurrentMenuId()
    {
        if (Request::get('m') != null) {
            Session::put('currentMenuId', Request::get('m'));
            Session::put('currentDashboardId', 0);

            return Request::get('m');
        } else {
            return Session::get('currentMenuId');
        }
    }

    public static function sidebarDashboard()
    {

        $menu = DB::table('cms_menus')->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".self::myPrivilegeId()."')")->where('is_dashboard', 1)->where('is_active', 1)->first();

        switch ($menu->type) {
            case 'Route':
                $url = route($menu->path);
                break;
            default:
            case 'URL':
                $url = $menu->path;
                break;
            case 'Controller & Method':
                $url = action($menu->path);
                break;
            case 'Module':
            case 'Statistic':
                $url = self::adminPath($menu->path);
                break;
        }

        @$menu->url = $url;

        return $menu;
    }

    public static function sidebarMenu()
    {
        $menu_active = DB::table('cms_menus')->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".self::myPrivilegeId()."')")->where('parent_id', 0)->where('is_active', 1)->where('is_dashboard', 0)->orderby('sorting', 'asc')->select('cms_menus.*')->get();

        foreach ($menu_active as &$menu) {

            try {
                switch ($menu->type) {
                    case 'Route':
                        $url = route($menu->path);
                        break;
                    default:
                    case 'URL':
                        $url = $menu->path;
                        break;
                    case 'Controller & Method':
                        $url = action($menu->path);
                        break;
                    case 'Module':
                    case 'Statistic':
                        $url = self::adminPath($menu->path);
                        break;
                }

                $menu->is_broken = false;
            } catch (\Exception $e) {
                $url = "#";
                $menu->is_broken = true;
            }

            $menu->url = $url;
            $menu->url_path = trim(str_replace(url('/'), '', $url), "/");

            $child = DB::table('cms_menus')->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".self::myPrivilegeId()."')")->where('is_dashboard', 0)->where('is_active', 1)->where('parent_id', $menu->id)->select('cms_menus.*')->orderby('sorting', 'asc')->get();
            if (count($child)) {

                foreach ($child as &$c) {

                    try {
                        switch ($c->type) {
                            case 'Route':
                                $url = route($c->path);
                                break;
                            default:
                            case 'URL':
                                $url = $c->path;
                                break;
                            case 'Controller & Method':
                                $url = action($c->path);
                                break;
                            case 'Module':
                            case 'Statistic':
                                $url = self::adminPath($c->path);
                                break;
                        }
                        $c->is_broken = false;
                    } catch (\Exception $e) {
                        $url = "#";
                        $c->is_broken = true;
                    }

                    $c->url = $url;
                    $c->url_path = trim(str_replace(url('/'), '', $url), "/");
                }

                $menu->children = $child;
            }
        }

        return $menu_active;
    }

    public static function deleteConfirm($redirectTo)
    {
        echo "swal({
				title: \"".cbLang('delete_title_confirm')."\",
				text: \"".cbLang('delete_description_confirm')."\",
				type: \"warning\",
				showCancelButton: true,
				confirmButtonColor: \"#ff0000\",
				confirmButtonText: \"".cbLang('confirmation_yes')."\",
				cancelButtonText: \"".cbLang('confirmation_no')."\",
				closeOnConfirm: false },
				function(){  location.href=\"$redirectTo\" });";
    }

    public static function getModulePath()
    {
        // Check to position of admin_path
        if(config("crudbooster.ADMIN_PATH")) {
            $adminPathSegments = explode('/', Request::path());
            $no = 1;
            foreach($adminPathSegments as $path) {
                if($path == config("crudbooster.ADMIN_PATH")) {
                    $segment = $no+1;
                    break;
                }
                $no++;
            }
        } else {
            $segment = 1;
        }

        return Request::segment($segment);
    }

    public static function mainpath($path = null)
    {

        $controllername = str_replace(["\crocodicstudio\crudbooster\controllers\\", "App\Http\Controllers\\"], "", strtok(Route::currentRouteAction(), '@'));
        $route_url = route($controllername.'GetIndex');

        if ($path) {
            if (substr($path, 0, 1) == '?') {
                return trim($route_url, '/').$path;
            } else {
                return $route_url.'/'.$path;
            }
        } else {
            return trim($route_url, '/');
        }
    }

    public static function adminPath($path = null)
    {
        return url(config('crudbooster.ADMIN_PATH').'/'.$path);
    }

    public static function getCurrentId()
    {
        $id = Session::get('current_row_id');
        $id = intval($id);
        $id = (! $id) ? Request::segment(4) : $id;
        $id = intval($id);

        return $id;
    }

    public static function getCurrentMethod()
    {
        $action = str_replace("App\Http\Controllers", "", Route::currentRouteAction());
        $atloc = strpos($action, '@') + 1;
        $method = substr($action, $atloc);

        return $method;
    }

    public static function clearCache($name)
    {
        if (Cache::forget($name)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isColumnNULL($table, $field)
    {
        if (Cache::has('field_isNull_'.$table.'_'.$field)) {
            return Cache::get('field_isNull_'.$table.'_'.$field);
        }

        try {
            //MySQL & SQL Server
            $isNULL = DB::select(DB::raw("select IS_NULLABLE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->IS_NULLABLE;
            $isNULL = ($isNULL == 'YES') ? true : false;
            Cache::forever('field_isNull_'.$table.'_'.$field, $isNULL);
        } catch (\Exception $e) {
            $isNULL = false;
            Cache::forever('field_isNull_'.$table.'_'.$field, $isNULL);
        }

        return $isNULL;
    }

    public static function getFieldType($table, $field)
    {
        if (Cache::has('field_type_'.$table.'_'.$field)) {
            return Cache::get('field_type_'.$table.'_'.$field);
        }

        $typedata = Cache::rememberForever('field_type_'.$table.'_'.$field, function () use ($table, $field) {

            try {
                //MySQL & SQL Server
                $typedata = DB::select(DB::raw("select DATA_TYPE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->DATA_TYPE;
            } catch (\Exception $e) {

            }

            if (! $typedata) {
                $typedata = 'varchar';
            }

            return $typedata;
        });

        return $typedata;
    }

    public static function getValueFilter($field)
    {
        $filter = Request::get('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['value'];
        }
    }

    public static function getSortingFilter($field)
    {
        $filter = Request::get('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['sorting'];
        }
    }

    public static function getTypeFilter($field)
    {
        $filter = Request::get('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['type'];
        }
    }

    public static function stringBetween($string, $start, $end)
    {
        $string = ' '.$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    public static function timeAgo($datetime_to, $datetime_from = null, $full = false)
    {
        $datetime_from = ($datetime_from) ?: date('Y-m-d H:i:s');
        $now = new \DateTime;
        if ($datetime_from != '') {
            $now = new \DateTime($datetime_from);
        }
        $ago = new \DateTime($datetime_to);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k.' '.$v.($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (! $full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string).' ' : 'just now';
    }

    public static function sendEmailQueue($queue)
    {
        \Config::set('mail.driver', self::getSetting('smtp_driver'));
        \Config::set('mail.host', self::getSetting('smtp_host'));
        \Config::set('mail.port', self::getSetting('smtp_port'));
        \Config::set('mail.username', self::getSetting('smtp_username'));
        \Config::set('mail.password', self::getSetting('smtp_password'));

        $html = $queue->email_content;
        $to = $queue->email_recipient;
        $subject = $queue->email_subject;
        $from_email = $queue->email_from_email;
        $from_name = $queue->email_from_name;
        $cc_email = $queue->email_cc_email;
        $attachments = unserialize($queue->email_attachments);

        \Mail::send("crudbooster::emails.blank", ['content' => $html], function ($message) use (
            $html,
            $to,
            $subject,
            $from_email,
            $from_name,
            $cc_email,
            $attachments
        ) {
            $message->priority(1);
            $message->to($to);
            $message->from($from_email, $from_name);
            $message->cc($cc_email);

            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($subject);
        });
    }

    public static function sendEmail($config = [])
    {
        if(\Config::get('mail.driver') == self::getSetting( 'smtp_driver' )) {
            \Config::set( 'mail.host', self::getSetting( 'smtp_host' ) );
            \Config::set( 'mail.port', self::getSetting( 'smtp_port' ) );
            \Config::set( 'mail.username', self::getSetting( 'smtp_username' ) );
            \Config::set( 'mail.password', self::getSetting( 'smtp_password' ) );
        }

        $to = $config['to'];
        $data = $config['data'];
        $template = $config['template'];

        $template = CRUDBooster::first('cms_email_templates', ['slug' => $template]);
        $html = $template->content;
        foreach ($data as $key => $val) {
            $html = str_replace('['.$key.']', $val, $html);
            $template->subject = str_replace('['.$key.']', $val, $template->subject);
        }
        $subject = $template->subject;
        $attachments = ($config['attachments']) ?: [];

        if ($config['send_at'] != null) {
            $a = [];
            $a['send_at'] = $config['send_at'];
            $a['email_recipient'] = $to;
            $a['email_from_email'] = $template->from_email ?: CRUDBooster::getSetting('email_sender');
            $a['email_from_name'] = $template->from_name ?: CRUDBooster::getSetting('appname');
            $a['email_cc_email'] = $template->cc_email;
            $a['email_subject'] = $subject;
            $a['email_content'] = $html;
            $a['email_attachments'] = serialize($attachments);
            $a['is_sent'] = 0;
            DB::table('cms_email_queues')->insert($a);

            return true;
        }

        \Mail::send("crudbooster::emails.blank", ['content' => $html], function ($message) use ($to, $subject, $template, $attachments) {
            $message->priority(1);
            $message->to($to);

            if ($template->from_email) {
                $from_name = ($template->from_name) ?: CRUDBooster::getSetting('appname');
                $message->from($template->from_email, $from_name);
            }

            if ($template->cc_email) {
                $message->cc($template->cc_email);
            }

            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($subject);
        });
    }

    public static function valid($arr = [], $type = 'json')
    {
        $input_arr = Request::all();

        foreach ($arr as $a => $b) {
            if (is_int($a)) {
                $arr[$b] = 'required';
            } else {
                $arr[$a] = $b;
            }
        }

        $validator = Validator::make($input_arr, $arr);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            if ($type == 'json') {
                $result = [];
                $result['api_status'] = 0;
                $result['api_message'] = implode(', ', $message);
                $res = response()->json($result, 200);
                $res->send();
                exit;
            } else {
                $res = redirect()->back()->with(['message' => implode('<br/>', $message), 'message_type' => 'warning'])->withInput();
                \Session::driver()->save();
                $res->send();
                exit;
            }
        }
    }

    public static function parseSqlTable($table)
    {

        $f = explode('.', $table);

        if (count($f) == 1) {
            return ["table" => $f[0], "database" => config('crudbooster.MAIN_DB_DATABASE')];
        } elseif (count($f) == 2) {
            return ["database" => $f[0], "table" => $f[1]];
        } elseif (count($f) == 3) {
            return ["table" => $f[0], "schema" => $f[1], "table" => $f[2]];
        }

        return false;
    }

    public static function putCache($section, $cache_name, $cache_value)
    {
        if (Cache::has($section)) {
            $cache_open = Cache::get($section);
        } else {
            Cache::forever($section, []);
            $cache_open = Cache::get($section);
        }
        $cache_open[$cache_name] = $cache_value;
        Cache::forever($section, $cache_open);

        return true;
    }

    public static function getCache($section, $cache_name)
    {

        if (Cache::has($section)) {
            $cache_open = Cache::get($section);

            return $cache_open[$cache_name];
        } else {
            return false;
        }
    }

    public static function flushCache()
    {
        Cache::flush();
    }

    public static function forgetCache($section, $cache_name)
    {
        if (Cache::has($section)) {
            $open = Cache::get($section);
            unset($open[$cache_name]);
            Cache::forever($section, $open);

            return true;
        } else {
            return false;
        }
    }

    public static function pk($table)
    {
        return self::findPrimaryKey($table);
    }

//     public static function findPrimaryKey($table)
//     {
//         if (! $table) {
//             return 'id';
//         }

//         if (self::getCache('table_'.$table, 'primary_key')) {
//             return self::getCache('table_'.$table, 'primary_key');
//         }
//         $table = CRUDBooster::parseSqlTable($table);

//         if (! $table['table']) {
//             throw new \Exception("parseSqlTable can't determine the table");
//         }
//         $query = config('database.connections.'.config('database.default').'.driver') == 'pgsql' ? "select * from information_schema.key_column_usage WHERE TABLE_NAME = '$table[table]'" : "select * from information_schema.COLUMNS where TABLE_SCHEMA = '$table[database]' and TABLE_NAME = '$table[table]' and COLUMN_KEY = 'PRI'";
//         $keys = DB::select($query);
//         $primary_key = $keys[0]->COLUMN_NAME;
//         if ($primary_key) {
//             self::putCache('table_'.$table, 'primary_key', $primary_key);

//             return $primary_key;
//         } else {
//             return 'id';
//         }
//     }

    public static function findPrimaryKey($table)
    {
        if(!$table)
        {
            return 'id';
        }

        $pk = DB::getDoctrineSchemaManager()->listTableDetails($table)->getPrimaryKey();
        if(!$pk) {
            return null;
        }
        return $pk->getColumns()[0];
    }

    public static function newId($table)
    {
        $key = CRUDBooster::findPrimaryKey($table);
        $id = DB::table($table)->max($key) + 1;

        return $id;
    }

    public static function isColumnExists($table, $field)
    {

        if (! $table) {
            throw new Exception("\$table is empty !", 1);
        }
        if (! $field) {
            throw new Exception("\$field is empty !", 1);
        }

        $table = CRUDBooster::parseSqlTable($table);

        // if(self::getCache('table_'.$table,'column_'.$field)) {
        // 	return self::getCache('table_'.$table,'column_'.$field);
        // }

        if (Schema::hasColumn($table['table'], $field)) {
            // self::putCache('table_'.$table,'column_'.$field,1);
            return true;
        } else {
            // self::putCache('table_'.$table,'column_'.$field,0);
            return false;
        }
    }

    public static function getForeignKey($parent_table, $child_table)
    {
        $parent_table = CRUDBooster::parseSqlTable($parent_table)['table'];
        $child_table = CRUDBooster::parseSqlTable($child_table)['table'];
        if (Schema::hasColumn($child_table, 'id_'.$parent_table)) {
            return 'id_'.$parent_table;
        } else {
            return $parent_table.'_id';
        }
    }

    public static function getTableForeignKey($fieldName)
    {
        $table = null;
        if (substr($fieldName, 0, 3) == 'id_') {
            $table = substr($fieldName, 3);
        } elseif (substr($fieldName, -3) == '_id') {
            $table = substr($fieldName, 0, (strlen($fieldName) - 3));
        }

        return $table;
    }

    public static function isForeignKey($fieldName)
    {
        if (substr($fieldName, 0, 3) == 'id_') {
            $table = substr($fieldName, 3);
        } elseif (substr($fieldName, -3) == '_id') {
            $table = substr($fieldName, 0, (strlen($fieldName) - 3));
        }

        if (Cache::has('isForeignKey_'.$fieldName)) {
            return Cache::get('isForeignKey_'.$fieldName);
        } else {
            if ($table) {
                $hasTable = Schema::hasTable($table);
                if ($hasTable) {
                    Cache::forever('isForeignKey_'.$fieldName, true);

                    return true;
                } else {
                    Cache::forever('isForeignKey_'.$fieldName, false);

                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public static function urlFilterColumn($key, $type, $value = '', $singleSorting = true)
    {
        $params = Request::all();
        $mainpath = trim(self::mainpath(), '/');

        if ($params['filter_column'] && $singleSorting) {
            foreach ($params['filter_column'] as $k => $filter) {
                foreach ($filter as $t => $val) {
                    if ($t == 'sorting') {
                        unset($params['filter_column'][$k]['sorting']);
                    }
                }
            }
        }

        $params['filter_column'][$key][$type] = $value;

        if (isset($params)) {
            return $mainpath.'?'.http_build_query($params);
        } else {
            return $mainpath.'?filter_column['.$key.']['.$type.']='.$value;
        }
    }

    public static function insertLog($description, $details = '')
    {
        if (CRUDBooster::getSetting('api_debug_mode')) {
            $a = [];
            $a['created_at'] = date('Y-m-d H:i:s');
            $a['ipaddress'] = $_SERVER['REMOTE_ADDR'];
            $a['useragent'] = $_SERVER['HTTP_USER_AGENT'];
            $a['url'] = Request::url();
            $a['description'] = $description;
            $a['details'] = $details;
            $a['id_cms_users'] = self::myId();
            DB::table('cms_logs')->insert($a);
        }
    }

    public static function referer()
    {
        return Request::server('HTTP_REFERER');
    }

    public static function listTables()
    {
        $tables = [];
        $multiple_db = config('crudbooster.MULTIPLE_DATABASE_MODULE');
        $multiple_db = ($multiple_db) ? $multiple_db : [];
        $db_database = config('crudbooster.MAIN_DB_DATABASE');

        if ($multiple_db) {
            try {
                $multiple_db[] = config('crudbooster.MAIN_DB_DATABASE');
                $query_table_schema = implode("','", $multiple_db);
                $tables = DB::select("SELECT CONCAT(TABLE_SCHEMA,'.',TABLE_NAME) FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA != 'mysql' AND TABLE_SCHEMA != 'performance_schema' AND TABLE_SCHEMA != 'information_schema' AND TABLE_SCHEMA != 'phpmyadmin' AND TABLE_SCHEMA IN ('$query_table_schema')");
            } catch (\Exception $e) {
                $tables = [];
            }
        } else {
            try {
                $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '".$db_database."'");
            } catch (\Exception $e) {
                $tables = [];
            }
        }

        return $tables;
    }

    public static function getUrlParameters($exception = null)
    {
        @$get = $_GET;
        $inputhtml = '';

        if ($get) {

            if (is_array($exception)) {
                foreach ($exception as $e) {
                    unset($get[$e]);
                }
            }

            $string_parameters = http_build_query($get);
            $string_parameters_array = explode('&', $string_parameters);
            foreach ($string_parameters_array as $s) {
                $part = explode('=', $s);
                $name = urldecode($part[0]);
                $value = urldecode($part[1]);
                if ($name) {
                    $inputhtml .= "<input type='hidden' name='$name' value='$value'/>\n";
                }
            }
        }

        return $inputhtml;
    }

    public static function authAPI()
    {
        $allowedUserAgent = config('crudbooster.API_USER_AGENT_ALLOWED');
        $user_agent = Request::header('User-Agent');
        $authorization = Request::header('Authorization');

        if ($allowedUserAgent && count($allowedUserAgent)) {
            $userAgentValid = false;
            foreach ($allowedUserAgent as $a) {
                if (stripos($user_agent, $a) !== false) {
                    $userAgentValid = true;
                    break;
                }
            }
            if ($userAgentValid == false) {
                $result['api_status'] = 0;
                $result['api_message'] = "THE DEVICE AGENT IS INVALID";
                $res = response()->json($result, 400);
                $res->send();
                exit;
            }
        }

        $accessToken = ltrim($authorization,"Bearer ");
        $accessTokenData = Cache::get("api_token_".$accessToken);
        if(!$accessTokenData) {
            response()->json([
                'api_status'=> 0,
                'api_message'=> 'Forbidden Access!'
            ], 403)->send();
            exit;
        }
    }

    public static function sendNotification($config = [])
    {
        $content = $config['content'];
        $to = $config['to'];
        $id_cms_users = $config['id_cms_users'];
        $id_cms_users = ($id_cms_users) ?: [CRUDBooster::myId()];
        foreach ($id_cms_users as $id) {
            $a = [];
            $a['created_at'] = date('Y-m-d H:i:s');
            $a['id_cms_users'] = $id;
            $a['content'] = $content;
            $a['is_read'] = 0;
            $a['url'] = $to;
            DB::table('cms_notifications')->insert($a);
        }

        return true;
    }

    public static function sendFCM($regID = [], $data)
    {
        if (! $data['title'] || ! $data['content']) {
            return 'title , content null !';
        }

        $apikey = CRUDBooster::getSetting('google_fcm_key');
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = [
            'registration_ids' => $regID,
            'data' => $data,
            'content_available' => true,
            'notification' => [
                'sound' => 'default',
                'badge' => 0,
                'title' => trim(strip_tags($data['title'])),
                'body' => trim(strip_tags($data['content'])),
            ],
            'priority' => 'high',
        ];
        $headers = [
            'Authorization:key='.$apikey,
            'Content-Type:application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $chresult = curl_exec($ch);
        curl_close($ch);

        return $chresult;
    }

    public static function getTableColumns($table)
    {
        //$cols = DB::getSchemaBuilder()->getColumnListing($table);
        $table = CRUDBooster::parseSqlTable($table);
        $cols = collect(DB::select('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table', [
            'database' => $table['database'],
            'table' => $table['table'],
        ]))->map(function ($x) {
            return (array) $x;
        })->toArray();

        $result = [];
        $result = $cols;

        $new_result = [];
        foreach ($result as $ro) {
            $new_result[] = $ro['COLUMN_NAME'];
        }

        return $new_result;
    }

    public static function getNameTable($columns)
    {
        $name_col_candidate = config('crudbooster.NAME_FIELDS_CANDIDATE');
        $name_col_candidate = explode(',', $name_col_candidate);
        $name_col = '';
        foreach ($columns as $c) {
            foreach ($name_col_candidate as $cc) {
                if (strpos($c, $cc) !== false) {
                    $name_col = $c;
                    break;
                }
            }
            if ($name_col) {
                break;
            }
        }
        if ($name_col == '') {
            $name_col = 'id';
        }

        return $name_col;
    }

    public static function isExistsController($table)
    {
        $controllername = ucwords(str_replace('_', ' ', $table));
        $controllername = str_replace(' ', '', $controllername).'Controller';
        $path = base_path("app/Http/Controllers/");
        $path2 = base_path("app/Http/Controllers/ControllerMaster/");
        if (file_exists($path.'Admin'.$controllername.'.php') || file_exists($path2.'Admin'.$controllername.'.php') || file_exists($path2.$controllername.'.php')) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateAPI($controller_name, $table_name, $permalink, $method_type = 'post')
    {
        $php = '
		<?php namespace App\Http\Controllers;

		use Session;
		use Request;
		use DB;
		use CRUDBooster;

		class Api'.$controller_name.'Controller extends \crocodicstudio\crudbooster\controllers\ApiController {

		    function __construct() {
				$this->table       = "'.$table_name.'";
				$this->permalink   = "'.$permalink.'";
				$this->method_type = "'.$method_type.'";
		    }
		';

        $php .= "\n".'
		    public function hook_before(&$postdata) {
		        //This method will be execute before run the main process

		    }';

        $php .= "\n".'
		    public function hook_query(&$query) {
		        //This method is to customize the sql query

		    }';

        $php .= "\n".'
		    public function hook_after($postdata,&$result) {
		        //This method will be execute after run the main process

		    }';

        $php .= "\n".'
		}
		';

        $php = trim($php);
        $path = base_path("app/Http/Controllers/");
        file_put_contents($path.'Api'.$controller_name.'Controller.php', $php);
    }

    public static function generateController($table, $name = null)
    {

        $exception = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $image_candidate = explode(',', config('crudbooster.IMAGE_FIELDS_CANDIDATE'));
        $password_candidate = explode(',', config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));
        $phone_candidate = explode(',', config('crudbooster.PHONE_FIELDS_CANDIDATE'));
        $email_candidate = explode(',', config('crudbooster.EMAIL_FIELDS_CANDIDATE'));
        $name_candidate = explode(',', config('crudbooster.NAME_FIELDS_CANDIDATE'));
        $url_candidate = explode(',', config("crudbooster.URL_FIELDS_CANDIDATE"));

        $controllername = ucwords(str_replace('_', ' ', $table));
        $controllername = str_replace(' ', '', $controllername).'Controller';
        if ($name) {
            $controllername = ucwords(str_replace(['_', '-'], ' ', $name));
            $controllername = str_replace(' ', '', $controllername).'Controller';
        }

        $path = base_path("app/Http/Controllers/");
        $countSameFile = count(glob($path.'Admin'.$controllername.'.php'));

        if ($countSameFile != 0) {
            $suffix = $countSameFile;
            $controllername = ucwords(str_replace(['_', '-'], ' ', $name)).$suffix;
            $controllername = str_replace(' ', '', $controllername).'Controller';
        }

        $coloms = CRUDBooster::getTableColumns($table);
        $name_col = CRUDBooster::getNameTable($coloms);
        $pk = CB::pk($table);

        $button_table_action = 'TRUE';
        $button_action_style = "button_icon";
        $button_add = 'TRUE';
        $button_edit = 'TRUE';
        $button_delete = 'TRUE';
        $button_show = 'TRUE';
        $button_detail = 'TRUE';
        $button_filter = 'TRUE';
        $button_export = 'FALSE';
        $button_import = 'FALSE';
        $button_bulk_action = 'TRUE';
        $global_privilege = 'FALSE';

        $php = '
<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class Admin'.$controllername.' extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {
	    	# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->table 			   = "'.$table.'";
			$this->title_field         = "'.$name_col.'";
			$this->limit               = 20;
			$this->orderby             = "'.$pk.',desc";
			$this->show_numbering      = FALSE;
			$this->global_privilege    = '.$global_privilege.';
			$this->button_table_action = '.$button_table_action.';
			$this->button_action_style = "'.$button_action_style.'";
			$this->button_add          = '.$button_add.';
			$this->button_delete       = '.$button_delete.';
			$this->button_edit         = '.$button_edit.';
			$this->button_detail       = '.$button_detail.';
			$this->button_show         = '.$button_show.';
			$this->button_filter       = '.$button_filter.';
			$this->button_export       = '.$button_export.';
			$this->button_import       = '.$button_import.';
			$this->button_bulk_action  = '.$button_bulk_action.';
			$this->sidebar_mode		   = "normal"; //normal,mini,collapse,collapse-mini
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
	        $this->col = [];
	';
        $coloms_col = array_slice($coloms, 0, 8);
        foreach ($coloms_col as $c) {
            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $label = str_replace('Cms ', '', $label);
            $field = $c;

            if (in_array($field, $exception)) {
                continue;
            }

            if (array_search($field, $password_candidate) !== false) {
                continue;
            }

            if (substr($field, 0, 3) == 'id_') {
                $jointable = str_replace('id_', '', $field);
                $joincols = CRUDBooster::getTableColumns($jointable);
                $joinname = CRUDBooster::getNameTable($joincols);
                $php .= "\t\t".'$this->col[] = array("label"=>"'.$label.'","name"=>"'.$field.'","join"=>"'.$jointable.','.$joinname.'");'."\n";
            } elseif (substr($field, -3) == '_id') {
                $jointable = substr($field, 0, (strlen($field) - 3));
                $joincols = CRUDBooster::getTableColumns($jointable);
                $joinname = CRUDBooster::getNameTable($joincols);
                $php .= "\t\t".'$this->col[] = array("label"=>"'.$label.'","name"=>"'.$field.'","join"=>"'.$jointable.','.$joinname.'");'."\n";
            } else {
                $image = '';
                if (in_array($field, $image_candidate)) {
                    $image = ',"image"=>true';
                }
                $php .= "\t\t".'$this->col[] = array("label"=>"'.$label.'","name"=>"'.$field.'" '.$image.');'."\n";
            }
        }

        $php .= "\n\t\t\t# END COLUMNS DO NOT REMOVE THIS LINE";

        $php .= "\n\t\t\t# START FORM DO NOT REMOVE THIS LINE";
        $php .= "\n\t\t".'$this->form = [];'."\n";

        foreach ($coloms as $c) {
            $attribute = [];
            $validation = [];
            $validation[] = 'required';
            $placeholder = '';
            $help = '';

            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $field = $c;

            if (in_array($field, $exception)) {
                continue;
            }

            $typedata = CRUDBooster::getFieldType($table, $field);

            switch ($typedata) {
                default:
                case 'varchar':
                case 'char':
                    $type = "text";
                    $validation[] = "min:1|max:255";
                    break;
                case 'text':
                case 'longtext':
                    $type = 'textarea';
                    $validation[] = "string|min:5|max:5000";
                    break;
                case 'date':
                    $type = 'date';
                    $validation[] = "date";
                    break;
                case 'datetime':
                case 'timestamp':
                    $type = 'datetime';
                    $validation[] = "date_format:Y-m-d H:i:s";
                    break;
                case 'time':
                    $type = 'time';
                    $validation[] = 'date_format:H:i:s';
                    break;
                case 'double':
                    $type = 'money';
                    $validation[] = "integer|min:0";
                    break;
                case 'int':
                case 'integer':
                    $type = 'number';
                    $validation[] = 'integer|min:0';
                    break;
            }

            if (substr($field, 0, 3) == 'id_') {
                $jointable = str_replace('id_', '', $field);
                $joincols = CRUDBooster::getTableColumns($jointable);
                $joinname = CRUDBooster::getNameTable($joincols);
                $attribute['datatable'] = $jointable.','.$joinname;
                $type = 'select2';
            }

            if (substr($field, -3) == '_id') {
                $jointable = str_replace('_id', '', $field);
                $joincols = CRUDBooster::getTableColumns($jointable);
                $joinname = CRUDBooster::getNameTable($joincols);
                $attribute['datatable'] = $jointable.','.$joinname;
                $type = 'select2';
            }

            if (substr($field, 0, 3) == 'is_') {
                $type = 'radio';
                $label_field = ucwords(substr($field, 3));
                $validation = ['required|integer'];
                $attribute['dataenum'] = ['1|'.$label_field, '0|Un-'.$label_field];
            }

            if (in_array($field, $password_candidate)) {
                $type = 'password';
                $validation = ['min:3', 'max:32'];
                $attribute['help'] = cbLang("text_default_help_password");
            }

            if (in_array($field, $image_candidate)) {
                $type = 'upload';
                $attribute['help'] = cbLang('text_default_help_upload');
                $validation = ['required|image|max:3000'];
            }

            if ($field == 'latitude') {
                $type = 'hidden';
            }
            if ($field == 'longitude') {
                $type = 'hidden';
            }

            if (in_array($field, $phone_candidate)) {
                $type = 'number';
                $validation = ['required', 'numeric'];
                $attribute['placeholder'] = cbLang('text_default_help_number');
            }

            if (in_array($field, $email_candidate)) {
                $type = 'email';
                $validation[] = 'email|unique:'.$table;
                $attribute['placeholder'] = cbLang('text_default_help_email');
            }

            if ($type == 'text' && in_array($field, $name_candidate)) {
                $attribute['placeholder'] = cbLang('text_default_help_text');
                $validation = ['required', 'string', 'min:3', 'max:70'];
            }

            if ($type == 'text' && in_array($field, $url_candidate)) {
                $validation = ['required', 'url'];
                $attribute['placeholder'] = cbLang('text_default_help_url');
            }

            $validation = implode('|', $validation);

            $php .= "\t\t";
            $php .= '$this->form[] = ["label"=>"'.$label.'","name"=>"'.$field.'","type"=>"'.$type.'","required"=>TRUE';

            if ($validation) {
                $php .= ',"validation"=>"'.$validation.'"';
            }

            if ($attribute) {
                foreach ($attribute as $key => $val) {
                    if (is_bool($val)) {
                        $val = ($val) ? "TRUE" : "FALSE";
                    } else {
                        $val = '"'.$val.'"';
                    }
                    $php .= ',"'.$key.'"=>'.$val;
                }
            }

            $php .= "];\n";
        }

        $php .= "\n\t\t\t# END FORM DO NOT REMOVE THIS LINE";

        $php .= '

			/*
	        | ----------------------------------------------------------------------
	        | Sub Module
	        | ----------------------------------------------------------------------
			| @label          = Label of action
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        |
	        */
	        $this->sub_module = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------
	        | @label       = Label of action
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        |
	        */
	        $this->addaction = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add More Button Selected
	        | ----------------------------------------------------------------------
	        | @label       = Label of action
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button
	        | Then about the action, you should code at actionButtonSelected method
	        |
	        */
	        $this->button_selected = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------
	        | @message = Text of message
	        | @type    = warning,success,danger,info
	        |
	        */
	        $this->alert        = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add more button to header button
	        | ----------------------------------------------------------------------
	        | @label = Name of button
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        |
	        */
	        $this->index_button = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.
	        |
	        */
	        $this->table_row_color = array();


	        /*
	        | ----------------------------------------------------------------------
	        | You may use this bellow array to add statistic at dashboard
	        | ----------------------------------------------------------------------
	        | @label, @count, @icon, @color
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add javascript at body
	        | ----------------------------------------------------------------------
	        | javascript code in the variable
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = NULL;


            /*
	        | ----------------------------------------------------------------------
	        | Include HTML Code before index table
	        | ----------------------------------------------------------------------
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;



	        /*
	        | ----------------------------------------------------------------------
	        | Include HTML Code after index table
	        | ----------------------------------------------------------------------
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;



	        /*
	        | ----------------------------------------------------------------------
	        | Include Javascript File
	        | ----------------------------------------------------------------------
	        | URL of your javascript each array
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add css style at body
	        | ----------------------------------------------------------------------
	        | css code in the variable
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;



	        /*
	        | ----------------------------------------------------------------------
	        | Include css File
	        | ----------------------------------------------------------------------
	        | URL of your css each array
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();


	    }


	    /*
	    | ----------------------------------------------------------------------
	    | Hook for button selected
	    | ----------------------------------------------------------------------
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here

	    }


	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate query of index result
	    | ----------------------------------------------------------------------
	    | @query = current sql query
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate row of index table html
	    | ----------------------------------------------------------------------
	    |
	    */
	    public function hook_row_index($column_index,&$column_value) {
	    	//Your code here
	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate data input before add data is execute
	    | ----------------------------------------------------------------------
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after add public static function called
	    | ----------------------------------------------------------------------
	    | @id = last insert id
	    |
	    */
	    public function hook_after_add($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate data input before update data is execute
	    | ----------------------------------------------------------------------
	    | @postdata = input post data
	    | @id       = current id
	    |
	    */
	    public function hook_before_edit(&$postdata,$id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_after_edit($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }



	    //By the way, you can still create your own method in here... :)


	}
	        ';

        $php = trim($php);

        //create file controller
        file_put_contents($path.'Admin'.$controllername.'.php', $php);

        return 'Admin'.$controllername;
    }

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Alternate route for Laravel Route::controller
    | --------------------------------------------------------------------------------------------------------------
    | $prefix       = path of route
    | $controller   = controller name
    | $namespace    = namespace of controller (optional)
    |
    */
    public static function routeController($prefix, $controller, $namespace = null)
    {

        $prefix = trim($prefix, '/').'/';

        $namespace = ($namespace) ?: 'App\Http\Controllers';

        try {
            Route::get($prefix, ['uses' => $controller.'@getIndex', 'as' => $controller.'GetIndex']);

            $controller_class = new \ReflectionClass($namespace.'\\'.$controller);
            $controller_methods = $controller_class->getMethods(\ReflectionMethod::IS_PUBLIC);
            $wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';
            foreach ($controller_methods as $method) {

                if ($method->class != 'Illuminate\Routing\Controller' && $method->name != 'getIndex') {
                    if (substr($method->name, 0, 3) == 'get') {
                        $method_name = substr($method->name, 3);
                        $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                        $slug = strtolower(implode('-', $slug));
                        $slug = ($slug == 'index') ? '' : $slug;
                        Route::get($prefix.$slug.$wildcards, ['uses' => $controller.'@'.$method->name, 'as' => $controller.'Get'.$method_name]);
                    } elseif (substr($method->name, 0, 4) == 'post') {
                        $method_name = substr($method->name, 4);
                        $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                        Route::post($prefix.strtolower(implode('-', $slug)).$wildcards, [
                            'uses' => $controller.'@'.$method->name,
                            'as' => $controller.'Post'.$method_name,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {

        }
    }
}
