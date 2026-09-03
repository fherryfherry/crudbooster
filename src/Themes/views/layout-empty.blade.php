<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ basicInfoSetting()->getAppName() ?? 'CRUDBooster' }}</title>
    <link rel="shortcut icon" href="{{ appearanceSetting()->getFavicon() ? getStorageUrl(appearanceSetting()->getFavicon()) : asset('vendor/crudbooster/themes/assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{asset('vendor/crudbooster/themes/assets/css/app.min.css')}}">
    @cbAssets
</head>
<body class="antialiased">
@livewire('alert-message')
{{$slot}}
</body>
</html>
