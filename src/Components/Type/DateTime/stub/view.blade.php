{{-- There are variables $column, $value, and $formData that you can use --}}
@php
    $format = $column['option']['format'] ?? (config('cb.datetime_format') ?? 'Y-m-d H:i:s');
    $appTz = config('app.timezone') ?? 'UTC';
    $displayTz = $column['option']['timezone'] ?? null;
    $output = null;
    if (!empty($value)) {
        $c = \Carbon\Carbon::parse($value, $appTz);
        if ($displayTz) { $c->setTimezone($displayTz); }
        $output = $c->format($format);
    }
@endphp
<p>{{ $output }}</p>
