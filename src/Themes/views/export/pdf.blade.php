<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #222; padding: 6px 4px; word-break: break-word; }
        th { background: #f5f5f5; font-weight: bold; }
        tfoot td { border: none; }
        .footer { position: fixed; left: 0; right: 0; bottom: 0; height: 30px; font-size: 10px; color: #888; }
        .footer .right { position: absolute; right: 0; text-align: right; width: 50%; }
        .footer .center { text-align: center; width: 100%; }
        /* Enforce landscape orientation across DomPDF versions */
        @page { size: A4 landscape; margin: 40px 25px 50px 25px; }
        @bottom-center { content: element(footer); }
    </style>
</head>
<body>
    @php
        function safe_utf8($str) {
            $str = preg_replace('/[[:^print:]]/', '', $str);
            $str = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
            return is_string($str) ? $str : '';
        }
        function get_value($row, $key) {
            // Support array/object rows, dot keys like "table.field",
            // and fallback to plain field without table prefix.
            // Additionally, support keys saved as underscores (e.g. "members_first_name" or "cb_role_users_name")
            // by converting either the first or the last underscore to a dot when needed.
            $plainKey = str_contains($key, '.') ? explode('.', $key)[1] : $key;
            $underscoreDotFirst = null;
            $underscoreDotLast = null;
            if (!str_contains($key, '.') && str_contains($key, '_')) {
                $posFirst = strpos($key, '_');
                if ($posFirst !== false) {
                    $underscoreDotFirst = substr($key, 0, $posFirst) . '.' . substr($key, $posFirst + 1);
                }
                $posLast = strrpos($key, '_');
                if ($posLast !== false) {
                    $underscoreDotLast = substr($key, 0, $posLast) . '.' . substr($key, $posLast + 1);
                }
            }

            // Deduplicate candidates while keeping order
            $candidates = [];
            foreach ([$key, $plainKey, $underscoreDotFirst, $underscoreDotLast] as $cand) {
                if ($cand !== null && !in_array($cand, $candidates, true)) {
                    $candidates[] = $cand;
                }
            }

            if (is_array($row)) {
                foreach ($candidates as $k) {
                    if (array_key_exists($k, $row)) return $row[$k];
                    $val = data_get($row, $k);
                    if (!is_null($val)) return $val;
                }
                return null;
            }
            if (is_object($row)) {
                foreach ($candidates as $k) {
                    $val = data_get($row, $k);
                    if (!is_null($val)) return $val;
                    if (method_exists($row, 'getAttribute')) {
                        $attr = $row->getAttribute($k);
                        if (!is_null($attr)) return $attr;
                    }
                    if (isset($row->{$k})) return $row->{$k};
                }
                return null;
            }
            return null;
        }
    @endphp
    <div class="title">{{ $title }}</div>
    <table>
        <thead>
            <tr>
                @foreach($columns as $col)
                    @if($col['exportable'])
                        <th>{{ safe_utf8($col['label']) }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($columns as $col)
                        @if($col['exportable'])
                            <td>{{ safe_utf8(strip_tags(isset($col['relation']) ? get_value($row, $col['relation']['key']) : get_value($row, $col['key']))) }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer" id="footer">
        <div class="center">Page <span class="pageNumber"></span> of <span class="totalPages"></span></div>
        <div class="right">{{ safe_utf8($appName) }}</div>
    </div>
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('if ($PAGE_COUNT > 1) {
                $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                $size = 9;
                $pdf->text(270, 820, "Page $PAGE_NUM of $PAGE_COUNT", $font, $size);
                $pdf->text(500, 820, "' . addslashes($appName) . '", $font, $size);
            }');
        }
    </script>
</body>
</html>