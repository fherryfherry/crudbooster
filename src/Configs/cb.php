<?php

/**
 * This file is used to set the configuration of CrudBooster.
 * Don't forget to run "php artisan config:cache" after you change this file to reflect the changes.
 */

return [
    /**
     * This is a config to set the path of the admin panel.
     */
    'admin_path' => 'cms',

    /**
     * This is a config to set CB to demo mode.
     */
    'demo_mode'=> env('CB_DEMO_MODE', false),
    'demo_username'=> env('CB_DEMO_USERNAME', 'admin'),
    'demo_password'=> env('CB_DEMO_PASSWORD', 'admin'),

    /**
     * This is a config to set the path of the dashboard.
     * After the user login, the user will be redirected to this path.
     */
    'dashboard_path' => 'dashboard',

    /**
     * This is a config to set the path of the profile livewire component.
     * If you want to use the default profile livewire component, you can set it to null.
     * Else, you can set it to your custom profile livewire component.
     * Example: 'App\Cb\Modules\Profile\Livewire\YourProfile::class'
     */
    'profile_component' => null,

    /**
     * This is a config to set the theme of CrudBooster.
     * By default, it will use the default theme.
     * If you want to use the custom theme, you can set it to your custom theme. Set it with absolute path.
     * Do this only if you know what you are doing and understand how themes work in CRUDBooster. The view files must be the same as the default CRUDBooster.
     * Example: app_path('themes/your-theme')
     */
    'theme_path' => null,

    /**
     * This config is used to hide fields on the detail page.
     * Developers can customize the fields to be hidden here.
     * For example, we want to hide the password and password_confirmation fields on the detail page since it is not necessary.
     */
    'hide_field_on_detail' => ['password', 'password_confirmation'],

    /**
     * This config is used to skip saving fields when they are empty.
     * Foe example, when the password field is empty, it will not be saved.
     */
    'ignore_save_on_empty' => ['password'],

    /**
     * This config is used to skip saving fields.
     * For example, when the password_confirmation field is empty, it will not be saved.
     */
    'ignore_save' => ['password_confirmation'],

    /**
     *  This config is used to set the default avatar.
     */
    'default_avatar' => 'https://placehold.co/128?text=No+Photo&font=roboto',
    'no_image_browse' => 'https://placehold.co/48',

    /**
     * This is a config to set which role is the super admin role.
     */
    'super_admin_role' => 'SUPER ADMIN',

    /**
     * This is a config to set the maximum import file size in kilobytes.
     */
    'max_import_size' => 1024, //

    /**
     * If you want to use the TinyMCE editor as commercial, you can set the key here.
     */
    'tinymce_key' => env('TINYMCE_KEY', 'gpl'),

    /**
     * This is a config to set the maximum export limit.
     */
    'max_export_limit'=> 100000,

    /**
     * Storage settings for upload/view helpers.
     * Set storage_disk to override filesystems.default (e.g., 's3').
     * storage_temporary_minutes controls temporary URL expiry for non-public disks.
     */
    'storage_disk' => env('CB_STORAGE_DISK', null),
    'storage_temporary_minutes' => env('CB_STORAGE_TEMPORARY_MINUTES', 5),

    /**
     * Cache Booster (Beta)
     * ====================
     * This config is used to set the cache booster.
     * If you want to enable the cache booster, you can set it to true.
     * The cache booster will cache the CRUDBooster page to make it load faster and reduce the server load.
     * CB will put all links with versioning query to make sure the page is always updated.
     * You need to setting bootstrap/app.php middleware to \CrudBooster\CacheBooster\Optimize::class
     */
    'cache_booster'=> [
        'enabled' => env('CACHE_BOOSTER', false),
        'expiry' => env('CACHE_BOOSTER_EXPIRY', 5), // in minutes
    ],

    /**
     * Audit Log
     * =========
     * Hybrid capture for CRUD/auth/request with strict masking.
     */
    'audit_log' => [
        'enabled' => env('CB_AUDIT_LOG_ENABLED', true),
        'retention_days' => (int) env('CB_AUDIT_LOG_RETENTION_DAYS', 90),
        'max_payload_length' => (int) env('CB_AUDIT_LOG_MAX_PAYLOAD_LENGTH', 4000),
        'masked_fields' => [
            'password',
            'password_confirmation',
            'token',
            'access_token',
            'refresh_token',
            'authorization',
            'api_key',
            'secret',
            'client_secret',
            'remember_token',
        ],
        'skip_paths' => [
            '/livewire/*',
            '/vendor/*',
            '/storage/*',
            '/_debugbar/*',
        ],
    ],
];
