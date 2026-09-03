<?php

namespace CrudBooster\Livewire\ColumnBuilder;

class Transform
{
    use WithTransformCaller;

    public const DATE_FORMAT_YMD = 'dateFormatYmd';
    public const EMAIL_LINK = 'emailLink';
    public const UPPER_CASE = 'upperCase';
    public const LOWER_CASE = 'lowerCase';
    public const CURRENCY = 'currency';
    public const PERCENTAGE = 'percentage';

    private static function upperCase(): callable
    {
        return function ($row, $value) {
            return strtoupper($value);
        };
    }
    private static function lowerCase(): callable
    {
        return function ($row, $value) {
            return strtolower($value);
        };
    }
    private static function currency(): callable
    {
        return function ($row, $value) {
            return number_format($value, 2);
        };
    }
    private static function percentage(): callable
    {
        return function ($row, $value) {
            return number_format($value, 2) . '%';
        };
    }
    private static function emailLink(): callable
    {
        return function ($row, $value) {
            return "<a class='text-sky-600 hover:underline' href='mailto:$value'>$value</a>";
        };
    }
    private static function dateFormatYmd(): callable
    {
        return function ($row, $value) {
            return date('Y-m-d', strtotime($value));
        };
    }
}
