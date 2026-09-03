<?php

namespace CrudBooster\Components\Type;

class CBTypeRegistrar
{
    private static $types = [];

    public static function __getTypes($type, $key = null)
    {
        if ($key) {
            return static::$types[$type][$key] ?? null;
        } else {
            return static::$types[$type] ?? null;
        }
    }

    public static function __getAllType(): array
    {
        return static::$types;
    }

    public static function __getAllTypeGrouped(): array
    {
        $grouped = [];
        foreach (static::$types as $type) {
            $grouped[$type['group']][] = $type;
        }
        return $grouped;
    }

    public static function add($type, $form, $view, $clazz, $generalOption = true, $settingSupport = false): void
    {
        // check if the type is already registered
        if (isset(self::$types[$type])) {
            return;
        }
        self::$types[$type] = [
            'type' => $type,
            'group' => 'TEXT',
            'form' => $form,
            'view' => $view,
            'clazz' => $clazz,
            'settingSupport' => $settingSupport,
            'generalOption' => $generalOption
        ];
    }

    public static function addWysiwyg($type, $form, $view, $clazz, $generalOption = false, $settingSupport = false): void
    {
        // check if the type is already registered
        if (isset(self::$types[$type])) {
            return;
        }
        self::$types[$type] = [
            'type' => $type,
            'group' => 'WYSIWYG',
            'form' => $form,
            'view' => $view,
            'clazz' => $clazz,
            'settingSupport' => $settingSupport,
            'generalOption' => $generalOption
        ];
    }

    public static function addText($type, $form, $view, $clazz, $generalOption = true, $settingSupport = false): void
    {
        // check if the type is already registered
        if (isset(self::$types[$type])) {
            return;
        }
        self::$types[$type] = [
            'type' => $type,
            'group' => 'TEXT',
            'form' => $form,
            'view' => $view,
            'clazz' => $clazz,
            'settingSupport' => $settingSupport,
            'generalOption' => $generalOption
        ];
    }

    public static function addNumeric($type, $form, $view, $clazz, $generalOption = false, $settingSupport = false): void
    {
        // check if the type is already registered
        if (isset(self::$types[$type])) {
            return;
        }
        self::$types[$type] = [
            'type' => $type,
            'group' => 'NUMERIC',
            'form' => $form,
            'view' => $view,
            'clazz' => $clazz,
            'settingSupport' => $settingSupport,
            'generalOption' => $generalOption
        ];
    }

    public static function addUpload($type, $form, $view, $clazz, $generalOption = false, $settingSupport = false): void
    {
        // check if the type is already registered
        if (isset(self::$types[$type])) {
            return;
        }
        self::$types[$type] = [
            'type' => $type,
            'group' => 'UPLOAD',
            'form' => $form,
            'view' => $view,
            'clazz' => $clazz,
            'settingSupport' => $settingSupport,
            'generalOption' => $generalOption
        ];
    }

    public static function addPassword($type, $form, $view, $clazz, $generalOption = false, $settingSupport = false): void
    {
        // check if the type is already registered
        if (isset(self::$types[$type])) {
            return;
        }
        self::$types[$type] = [
            'type' => $type,
            'group' => 'PASSWORD',
            'form' => $form,
            'view' => $view,
            'clazz' => $clazz,
            'settingSupport' => $settingSupport,
            'generalOption' => $generalOption
        ];
    }

    public static function addJson($type, $form, $view, $clazz, $generalOption = false, $settingSupport = false): void
    {
        // check if the type is already registered
        if (isset(self::$types[$type])) {
            return;
        }
        self::$types[$type] = [
            'type' => $type,
            'group' => 'JSON',
            'form' => $form,
            'view' => $view,
            'clazz' => $clazz,
            'settingSupport' => $settingSupport,
            'generalOption' => $generalOption
        ];
    }

    public static function addSelect(array $data): void
    {
        // check if the type is already registered
        if (isset(self::$types[$data['type']])) {
            return;
        }
        self::$types[$data['type']] = [
            'type' => $data['type'],
            'group' => 'SELECT',
            'form' => $data['form'],
            'view' => $data['view'],
            'clazz' => $data['clazz'],
            'settingFormConfig' => $data['settingFormConfig']??null,
            'settingSupport' => $data['settingSupport']??false,
            'generalOption' => $data['generalOption']??false
        ];
    }

    public static function addDateTime(array $config): void
    {
        // check if the type is already registered
        if (isset(self::$types[ $config['type'] ])) {
            return;
        }
        self::$types[$config['type']] = [
            'type' => $config['type'],
            'group' => 'DATETIME',
            'form' => $config['form'],
            'view' => $config['view'],
            'clazz' => $config['clazz'],
            'settingSupport' => $config['settingSupport'] ?? false,
            'generalOption' => $config['generalOption'] ?? false,
            'optionList' => $config['optionList'] ?? null
        ];
    }

    public static function addMap(array $config): void
    {
        // check if the type is already registered
        if (isset(self::$types[ $config['type'] ])) {
            return;
        }
        self::$types[$config['type']] = [
            'type' => $config['type'],
            'group' => 'MAP',
            'form' => $config['form'],
            'view' => $config['view'],
            'clazz' => $config['clazz'],
            'settingSupport' => $config['settingSupport'] ?? false,
            'generalOption' => $config['generalOption'] ?? false,
            'optionList' => $config['optionList'] ?? null
        ];
    }
}
