{{-- There are variables $column, $value, and $formData that you can use --}}
<p>{{ $value ? date($column['option']['format'] ?? (config('cb.date_format') ?? 'Y-m-d'), strtotime($value)) : null }}</p>
