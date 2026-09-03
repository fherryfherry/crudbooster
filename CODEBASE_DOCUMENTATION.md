# CRUDBooster Codebase Documentation

Dokumentasi ini disusun dari korelasi antara dokumentasi publik CRUDBooster dan implementasi aktual di codebase package `crudbooster/crudbooster` versi `7.14.1`.

Tujuan dokumen ini:

- memetakan arsitektur package
- menjelaskan lifecycle runtime dan generator
- mendokumentasikan modul bawaan, tipe field, command, dan extension point
- menjadi referensi kerja saat maintenance, debugging, dan pengembangan fitur baru

## 1. Ringkasan Arsitektur

Secara arsitektural, CRUDBooster v7 adalah package Laravel berbasis:

- Laravel package service provider
- Livewire sebagai engine UI utama
- Blade views untuk layout dan rendering
- builder pattern untuk browse columns dan form fields
- PHP attributes untuk hook lifecycle
- service layer untuk akses model/query
- code generator untuk membuat module CRUD di `app/Cb/Modules`

Secara praktik, package ini bukan sekadar "generator CRUD", tetapi sebuah mini admin framework yang menyediakan:

- auth area
- dashboard
- role and permission
- menu management
- setting management
- module builder
- page builder
- query builder
- registry untuk custom field type, custom setting, custom page element, custom module

## 2. Entry Point Package

Entry point utama package adalah:

- `src/CBServiceProvider.php`

Tanggung jawab `CBServiceProvider`:

- merge config `cb.php`
- load route dasar package dari `src/router.php`
- register provider utama:
  - `CbComponentServiceProvider`
  - `CbLiveWireServiceProvider`
  - `CbThemeServiceProvider`
  - `CbModuleServiceProvider`
- register command artisan:
  - `cb:install`
  - `cb:crud`
  - `cb:setting`
  - `cb:custom-css`
- register helper global via `src/Helpers/Common.php`
- register facade singleton `SchemaUtil`

Kesimpulan: seluruh package diboot dari satu provider utama, lalu dipecah ke domain yang lebih kecil lewat provider turunan.

## 3. Struktur Folder Utama

### 3.1 Folder inti

- `src/Commands`
  - command artisan untuk install, generate CRUD, generate setting, dan css template
- `src/Livewire`
  - abstraksi inti browse/form dan builder pendukung
- `src/Modules`
  - modul built-in dan generator module
- `src/Components`
  - reusable UI behavior dan field types
- `src/Attributes`
  - PHP attributes dan readers untuk lifecycle hooks
- `src/Domain/Services`
  - service layer
- `src/Helpers`
  - helper umum path, route, storage, schema, uploader, dll
- `src/Themes`
  - theme assets, blade layout, dan directive asset
- `src/Stubs`
  - stub bawaan untuk user module dan generator
- `src/Http/Controllers`
  - controller export CSV/XLS/PDF

### 3.2 Folder yang dihasilkan ke app host

Generator CRUDBooster menulis kode ke aplikasi host, terutama:

- `app/Cb/Modules/*`
- `app/Cb/Settings/*`

Jadi package ini runtime-nya hybrid:

- sebagian logika hidup di dalam package
- sebagian modul CRUD final di-generate ke app pengguna

## 4. Bootstrap dan Registrasi

### 4.1 Registrasi komponen

Provider: `src/Components/CbComponentServiceProvider.php`

Behavior:

- me-load views komponen dari folder `src/Components`
- auto-register semua `*ServiceProvider.php` di bawah `src/Components` menggunakan `CbLoader`

Artinya banyak behavior component terpasang secara discovery-based, bukan hardcoded satu per satu.

### 4.2 Registrasi Livewire common

Provider: `src/Livewire/CbLiveWireServiceProvider.php`

Behavior:

- me-load views Livewire
- me-include semua file `Common.php` di bawah `src/Livewire`

Pola ini dipakai untuk helper procedural kecil yang dibutuhkan trait atau komponen.

### 4.3 Registrasi theme

Provider: `src/Themes/CbThemeServiceProvider.php`

Behavior utama:

- load views theme default, atau override via `config('cb.theme_path')`
- publish assets ke `public/vendor/crudbooster/themes/assets`
- register Blade directives:
  - `@cbAssets`
  - `@cbForm`
  - `@cbFormTitle`
  - `@cbModalImport`
  - `@cbModalBulkConfirmation`
  - `@cbDetailContent`
- register Blade component `header`

Theme juga punya registrar asset:

- `src/Themes/CbThemeAssetRegistrar.php`

yang memungkinkan package atau app host mendaftarkan CSS/JS tambahan secara runtime.

### 4.4 Registrasi module

Provider: `src/Modules/CbModuleServiceProvider.php`

Modul bawaan yang diregistrasi:

- Auth
- Profile
- Role
- Menu
- Setting
- Dashboard
- Module Builder
- Page Builder
- Query Builder

Selain itu, package otomatis me-load service provider custom module dari:

- `app/Cb/Modules`

via trait `src/Modules/WithUserModuleLoader.php`.

Ini adalah fondasi extensibility utama CRUDBooster.

## 5. Konsep Module

### 5.1 Registry module

File:

- `src/Modules/ModuleRegistrar.php`

Setiap module yang valid diregistrasi dengan metadata:

- `key`
- `name`
- `mainPath`
- `browseModuleClass`
- `formModuleClass`
- `serviceProvider`
- `additional`

Saat module didaftarkan:

- browse Livewire component didaftarkan sebagai `{key}-browse`
- form Livewire component didaftarkan sebagai `{key}-form`
- semua komponen Livewire lain di folder module ikut di-auto-register

### 5.2 Bentuk module

Satu module CRUD tipikal terdiri dari:

- service provider module
- `router.php`
- browse Livewire component
- form/detail Livewire component
- model
- service
- optional migration

Struktur target generator:

- `app/Cb/Modules/{ModuleName}/...`

## 6. Konvensi Routing

Helper route inti:

- `src/Helpers/CBRoute.php`

Ada dua pola route:

### 6.1 CRUD route standar

`CBRoute::createRoute($path, $browseComponent, $formComponent)`

Menghasilkan:

- `/{admin_path}/{path}/` -> browse
- `/{admin_path}/{path}/{actionOne}` -> detail
- `/{admin_path}/{path}/{actionOne}/{actionTwo}` -> edit/create variant

Contoh:

- `menu`
- `user`

### 6.2 Single component route

`CBRoute::createRouteOne($path, $component)`

Dipakai untuk:

- setting
- page builder
- module builder step wizard
- query builder

### 6.3 Prefix admin path

Semua path dikonstruksi melalui:

- `getCmsPath()`
- `getCmsUrl()`
- `CBPathUtil::getCmsPath()`

Default prefix:

- `cms`

diatur lewat `config('cb.admin_path')`.

## 7. Lifecycle Runtime Komponen

Abstraksi runtime inti:

- `src/Livewire/BaseModuleAbstract.php`
- `src/Livewire/BaseBrowseAbstract.php`
- `src/Livewire/BaseBrowseComponent.php`
- `src/Livewire/BaseFormComponent.php`

### 7.1 BaseModuleAbstract

Tanggung jawab:

- deteksi browse path dari URL aktif
- resolve module metadata dari `ModuleRegistrar`
- jalankan `init()`
- panggil lifecycle attribute seperti `OnFormInit`, `OnFormHydrate`, `OnBrowseHydrate`

### 7.2 BaseBrowseComponent

Behavior utama:

- mount browse module
- cek authorization `read`
- simpan state filter/search/sort ke session
- bangun paginator dari service layer
- render browse table
- handle:
  - filter
  - search
  - sorting
  - import/export
  - bulk actions
  - action buttons
  - form dialog
  - sub module / master-detail
  - hook query
  - hook search
  - drag ordering

### 7.3 BaseFormComponent

Behavior utama:

- mount create/edit/detail form
- resolve `formId`
- load existing model data bila edit/detail
- cek authorization
- jalankan validation builder
- jalankan `OnFormSaving`, `OnFormValidated`, `OnFormSaved`
- simpan model dalam transaksi database
- handle redirect kembali via `ref` atau browse path
- handle parent-child relation lewat encrypted `parent-module`

Catatan penting:

- form detail dan form edit memakai komponen dasar yang sama
- redirect setelah save cukup sophisticated karena mempertimbangkan:
  - `ref`
  - `parent-module`
  - `redirectDetailOnSave`
  - `save` vs `saveAndMore`

## 8. Browse Builder

Browse table dibangun melalui:

- `src/Livewire/ColumnBuilder/Column.php`
- `src/Livewire/ColumnBuilder/WithColumnBuilder.php`

Kemampuan `Column`:

- definisi label dan key
- sortable, filterable, searchable, exportable
- transform value
- transform with full row
- relation one-to-many
- relation many-to-many
- nested relation
- image/badge helpers
- filter type khusus

Filter bawaan yang teridentifikasi:

- `contains`
- `>`
- `>=`
- `<`
- `<=`
- `date_range`
- `select_enum`
- `select_query`

Browse columns disimpan dalam dua representasi:

- `_browseColumns`
  - versi internal, masih mengandung closure
- `browseColumns`
  - versi aman untuk Livewire serialization

## 9. Form Builder

Form dibangun melalui:

- `src/Livewire/FormBuilder/Form.php`
- `src/Livewire/FormBuilder/WithFormBuilder.php`

### 9.1 Konsep dasar

Setiap field disusun sebagai metadata array yang dibungkus builder `Form`.

Contoh pattern:

- `Form::add(...)`
- `->required()`
- `->default(...)`
- `->live(...)`
- `->onChange(...)`
- `->transform(...)`
- `->transformValue(...)`
- `->transformDisplay(...)`
- `->readonly()`
- `->readonlyOn(...)`
- `->placeholder(...)`
- `->help(...)`
- `->option(...)`
- `->showDetail(...)`
- `->showEdit(...)`
- `->showCreate(...)`

### 9.2 Kemampuan form builder

Trait `WithFormBuilder` menangani:

- konversi builder ke array final
- pembersihan closure sebelum serialisasi Livewire
- konstruksi validation rules
- dynamic visibility:
  - `showEdit`
  - `showCreate`
  - `showOn`
- callback per kolom
- binding debounce
- `onChange` callback

### 9.3 Validasi

Validasi disusun dari field metadata, bukan ditulis manual sebagai array besar.

Ada logic khusus untuk file input:

- field `required` tidak dipaksa lagi jika file lama sudah ada

## 10. Type System

Field type bawaan diregistrasi via:

- `src/Components/Type/CbTypeServiceProvider.php`
- `src/Components/Type/CBTypeRegistrar.php`

Group type yang teridentifikasi di registrar:

- `TEXT`
- `NUMERIC`
- `UPLOAD`
- `PASSWORD`
- `JSON`
- `SELECT`
- `DATETIME`
- `MAP`
- `WYSIWYG`

### 10.1 Type bawaan yang terdaftar

Dari codebase saat ini, type bawaan meliputi:

- `text`
- `textarea`
- `email`
- `url`
- `password`
- `number`
- `money`
- `date`
- `datetime`
- `time`
- `radio`
- `checkbox`
- `select`
- `selectChips`
- `selectIcon`
- `jsonChecklist`
- `jsonTable`
- `image`
- `file`
- `trix`
- `tinymce`
- `summernote`
- `empty`

Setiap type umumnya terdiri dari:

- `*ServiceProvider.php`
- folder `stub/form.blade.php`
- folder `stub/view.blade.php`
- class function/option/helper type

Ini sesuai dengan dokumentasi publik yang menekankan custom input type berbasis:

- service provider
- form view
- detail view
- class logic type

## 11. Hook dan Event System

Salah satu fondasi penting CRUDBooster v7 adalah hook berbasis PHP attributes.

Entry point:

- `src/Attributes/*`
- `src/Attributes/WithAttributeCaller.php`

### 11.1 Jenis hook yang teridentifikasi

Hook form:

- `OnFormInit`
- `OnFormMounting`
- `OnFormMounted`
- `OnFormGettingData`
- `OnFormGetData`
- `OnFormSaving`
- `OnFormValidated`
- `OnFormSaved`
- `OnFormHydrate`
- `OnFormDehydrate`
- `OnFormRendering`

Hook browse:

- `OnBrowseMounting`
- `OnBrowseHydrate`
- `OnBrowseQueryCreating`
- `OnBrowseRendering`
- `OnBrowseColumnRendering`

Hook lain:

- `OnDragged`
- `OnPropertyUpdated`
- `OnDataDeleting`
- `OnDataDeleted`

### 11.2 Behavior hook

Trait `WithAttributeCaller`:

- membaca method bertanda attribute tertentu
- mengeksekusi method tersebut pada titik lifecycle yang sesuai
- dalam banyak kasus juga mem-publish event Laravel

Event yang ditemukan:

- `EventFormSaving`
- `EventFormValidated`
- `EventFormSaved`
- `EventFormGettingData`
- `EventFormGetData`
- `EventDataDeleting`
- `EventDataDeleted`
- `EventBrowseRendering`
- `EventBrowseColumnRendering`

Kesimpulan:

- extension yang direkomendasikan memang melalui hook dan event
- ini konsisten dengan dokumentasi publik yang menyarankan interception sebelum/sesudah save

## 12. Service Layer

Service contract:

- `src/Domain/Services/ServiceContract.php`

Implementasi dasar:

- `src/Domain/Services/BaseService.php`

### 12.1 Tanggung jawab BaseService

- introspeksi field model
- query builder dasar
- auto-filter soft delete
- import data
- relation join handling
- search query handling
- pagination terpusat
- operasi CRUD umum

### 12.2 Implikasi desain

Browse dan form component tidak langsung mengakses model terlalu dalam. Mereka lebih sering berbicara ke:

- `protected $modelService`

Ini membuat code modul generated tetap tipis dan konsisten.

## 13. Built-in Modules

### 13.1 Auth

Provider:

- `src/Modules/Auth/CbAuthServiceProvider.php`

Fitur:

- login
- forgot password
- reset password
- logout
- publikasi language file
- install callback untuk splash/logo awal

Route utama:

- `/cms/auth/login`
- `/cms/auth/forgot`
- `/cms/auth/password-reset/{token}`

### 13.2 Dashboard

Provider:

- `src/Modules/Dashboard/CbDashboardServiceProvider.php`

Role:

- landing page admin panel setelah login

### 13.3 Role Management

Provider:

- `src/Modules/Role/CBRoleServiceProvider.php`

Fitur:

- register Gate:
  - `is_super_admin`
  - `browse`
  - `create`
  - `read`
  - `update`
  - `delete`
- module permission matrix per role

Catatan implementasi:

- role pertama / super admin diperlakukan khusus
- module permission diambil dari metadata `permissionAvailable` milik module

### 13.4 Menu Management

Provider:

- `src/Modules/Menu/CbMenuServiceProvider.php`

Fitur:

- CRUD menu
- integrasi dengan module generated
- support draggable ordering

### 13.5 Setting Management

Provider:

- `src/Modules/Setting/CBSettingServiceProvider.php`

Fitur:

- registry halaman setting
- load setting default bawaan
- load custom setting dari `app/Cb/Settings`

Setting default yang terlihat di codebase:

- Basic Info
- Appearance
- Security

Registry:

- `src/Modules/Setting/CbSettingRegistrar.php`

Saat setting ditambahkan:

- Livewire component diregistrasi
- route `/cms/setting/{key}` dibuat otomatis

### 13.6 Module Builder

Provider:

- `src/Modules/ModuleBuilder/ModuleBuilderServiceProvider.php`

Fitur:

- GUI pembuat module
- wizard multi-step:
  - basic info
  - table schema
  - relationship
  - hook query
  - browse design
  - bulk action
  - action button
  - form design
  - form hook

Catatan:

- route modul ini diproteksi `cb.feature:crud_gui`
- builder ini tampaknya fitur berlisensi

### 13.7 Page Builder

Provider:

- `src/Modules/PageBuilder/PageBuilderServiceProvider.php`

Fitur:

- CRUD page builder
- studio/editor page
- viewer page
- element registry

Registry:

- `src/Modules/PageBuilder/Elements/PageBuilderElementRegistrar.php`

Element bawaan yang terlihat:

- Heading
- Paragraph
- Image
- Table
- Chart
- Google Map
- Box Counter

### 13.8 Query Builder

Provider:

- `src/Modules/QueryBuilder/CbQueryBuilderServiceProvider.php`

Fitur:

- builder query internal
- route diproteksi `cb.feature:query_builder`
- secara eksplisit mematikan strict mode MySQL saat boot

### 13.9 User Module

User module bukan ada langsung sebagai kode final di `src/Modules/User`, tetapi banyak dipasang lewat stub/generator:

- `src/Stubs/Modules/User/*`

Pattern ini menunjukkan user module adalah module generated bawaan untuk aplikasi host.

## 14. Code Generator

### 14.1 Command generator

Command:

- `cb:crud`
- `cb:setting`
- `cb:custom-css`

### 14.2 `cb:crud`

File:

- `src/Commands/GenerateCrudCommand.php`

Alur:

1. ambil schema tabel
2. bangun metadata form dengan `ModuleFormConstructor`
3. simpan metadata ke tabel `cb_modules`
4. build code module lewat `ModuleBuilder`
5. clear route cache

### 14.3 `ModuleFormConstructor`

File:

- `src/Modules/ModuleBuilder/Builder/ModuleFormConstructor.php`

Tanggung jawab:

- baca schema tabel target
- deteksi primary key
- buat default browse columns
- buat default form design list
- tentukan type awal field berdasarkan schema

### 14.4 `ModuleBuilder`

File:

- `src/Modules/ModuleBuilder/Builder/ModuleBuilder.php`

Tanggung jawab:

- hapus module lama jika ada
- buat direktori target
- generate:
  - service provider
  - router
  - Livewire browse component
  - Livewire form component
  - migration
  - model
  - service
- buat menu default
- jalankan `composer dumpautoload`
- clear route cache

### 14.5 `cb:setting`

File:

- `src/Commands/GenerateSettingCommand.php`

Generator ini membuat setting module custom ke:

- `app/Cb/Settings/{SettingName}`

yang berisi:

- service provider
- Livewire setting component
- view
- helper property object
- helper function global

## 15. Sistem Permission

Permission inti dibangun di provider role:

- `src/Modules/Role/CBRoleServiceProvider.php`

Permission per module didorong dari metadata:

- `permissionAvailable`

yang diset saat `ModuleRegistrar::registerModule(...)`.

Permission value yang digunakan di banyak tempat:

- `CREATE`
- `READ`
- `UPDATE`
- `DELETE`

Pemakaian permission terlihat di:

- browse authorization
- form authorization
- action button visibility
- bulk action visibility

## 16. Helpers Penting

### 16.1 Path dan URL helper

File:

- `src/Helpers/Common.php`
- `src/Helpers/CBPathUtil.php`

Helper utama:

- `getCmsPath()`
- `getCmsUrl()`
- `getEditPath()`
- `getDetailPath()`
- `getCreatePath()`

### 16.2 Storage helper

Juga di `src/Helpers/Common.php`:

- `getStorageUrl()`
- `getStorageFileSize()`
- `getStorageFileExists()`

Semua helper ini menghormati:

- `cb.storage_disk`
- `cb.storage_temporary_minutes`

### 16.3 Upload helper

File:

- `src/Helpers/CbUploader.php`

Support:

- upload dari `Illuminate\Http\File`
- upload dari Livewire temporary file
- upload dari standard `UploadedFile`

Pola path default:

- `public/{Y-m-d}/...`

## 17. Konfigurasi Penting

File:

- `src/Configs/cb.php`

Config publik paling penting:

- `admin_path`
- `dashboard_path`
- `profile_component`
- `theme_path`
- `hide_field_on_detail`
- `ignore_save_on_empty`
- `ignore_save`
- `default_avatar`
- `super_admin_role`
- `max_import_size`
- `tinymce_key`
- `max_export_limit`
- `storage_disk`
- `storage_temporary_minutes`
- `cache_booster.enabled`
- `cache_booster.expiry`

## 18. Korelasi Dengan Dokumentasi Publik

Berikut korelasi konsep dokumentasi publik dengan implementasi codebase:

### 18.1 "Custom input type"

Terverifikasi di codebase lewat:

- `CbTypeServiceProvider`
- `CBTypeRegistrar`
- pattern tiap folder `src/Components/Type/*`

Artinya konsep custom type di docs memang real dan menjadi extension path resmi.

### 18.2 "Hook before/after save"

Terverifikasi di codebase lewat:

- attribute `OnFormSaving`
- attribute `OnFormSaved`
- trait `WithAttributeCaller`
- event `EventFormSaving` dan `EventFormSaved`

### 18.3 "Master-detail relationship"

Terverifikasi secara konsep lewat:

- `src/Components/MasterDetail/SubModule.php`
- `src/Components/MasterDetail/WithMasterDetail.php`
- dukungan `foreignKey`, `parent-module`, dan browse/form mount parameter

### 18.4 "Module generator / CRUD builder"

Terverifikasi lewat:

- `cb:crud`
- `ModuleBuilder`
- `ModuleFormConstructor`
- modul GUI builder

### 18.5 "Page builder"

Terverifikasi lewat:

- module PageBuilder
- element registrar
- viewer/studio Livewire components

Kesimpulan umum:

- dokumentasi publik cukup selaras dengan codebase
- klaim fitur utama memang benar-benar tercermin di struktur package
- extension path yang didorong docs sesuai dengan extension path di source

## 19. Pola Ekstensi Resmi yang Aman

Kalau ingin menambah fitur tanpa merusak core, pola yang paling aman adalah:

### 18.1 Tambah custom module

Target:

- `app/Cb/Modules/{ModuleName}`

CRUDBooster akan auto-load service provider module tersebut.

### 18.2 Tambah custom setting page

Target:

- `app/Cb/Settings/{SettingName}`

Bisa dibuat cepat via:

- `php artisan cb:setting`

### 18.3 Tambah custom field type

Pattern:

- buat service provider type
- register ke `CBTypeRegistrar`
- sediakan `form.blade.php` dan `view.blade.php`

### 18.4 Tambah page builder element

Pattern:

- buat Livewire element viewer/config
- register via `PageBuilderElementRegistrar`

### 18.5 Tambah hook di module form/browse

Pattern:

- gunakan attribute seperti:
  - `#[OnFormSaving]`
  - `#[OnFormSaved]`
  - `#[OnBrowseRendering]`

Ini lebih aman daripada mengubah base class core.

### 18.6 Tambah asset theme

Gunakan:

- `CbThemeAssetRegistrar::addCss(...)`
- `CbThemeAssetRegistrar::addJs(...)`

### 18.7 Override theme

Gunakan:

- `config('cb.theme_path')`

dengan struktur view yang kompatibel dengan default theme.

## 20. Risiko dan Catatan Teknis

Beberapa hal penting yang perlu dicatat saat maintenance:

### 18.1 Banyak registry bersifat static

Contoh:

- `ModuleRegistrar`
- `CBTypeRegistrar`
- `CbSettingRegistrar`
- `PageBuilderElementRegistrar`
- `CbThemeAssetRegistrar`

Implikasi:

- mudah dipakai
- tapi perlu hati-hati di test suite dan lifecycle request panjang

### 18.2 Banyak discovery berbasis scan filesystem

Contoh:

- `CbLoader::loadServiceProviders`
- `LivewireComponentRegistrar::autoRegister`
- helper `getModelList()`

Implikasi:

- fleksibel
- namun performa dan class discovery bisa sensitif terhadap struktur file app host

### 18.3 Generator memakai `shell_exec("composer dumpautoload")`

Ini terlihat di `ModuleBuilder::composerDump()`.

Implikasi:

- praktis
- tetapi rentan pada environment shared hosting, sandbox, atau disabled shell functions

### 18.4 Query builder dan relation search cukup kompleks

`BaseService` memuat banyak logic SQL khusus driver untuk relation dan search.

Implikasi:

- area ini kemungkinan besar jadi hotspot bug saat menambah feature browse/filter baru

## 21. Rekomendasi Membaca Codebase

Urutan baca paling efektif untuk engineer baru:

1. `src/CBServiceProvider.php`
2. `src/Modules/CbModuleServiceProvider.php`
3. `src/Modules/ModuleRegistrar.php`
4. `src/Livewire/BaseModuleAbstract.php`
5. `src/Livewire/BaseBrowseComponent.php`
6. `src/Livewire/BaseFormComponent.php`
7. `src/Livewire/ColumnBuilder/*`
8. `src/Livewire/FormBuilder/*`
9. `src/Attributes/*` dan `WithAttributeCaller`
10. `src/Domain/Services/BaseService.php`
11. `src/Modules/Role/*`
12. `src/Modules/ModuleBuilder/*`
13. `src/Components/Type/*`

## 22. Kesimpulan

CRUDBooster v7 di codebase ini adalah:

- Laravel package admin framework
- Livewire-first
- metadata-driven untuk form dan browse
- extensible lewat registrars, hooks, stubs, dan generated modules
- cukup konsisten dengan dokumentasi publik resmi

Kalau ingin bekerja aman di package ini, prinsip praktisnya:

- pahami base browse/form dulu
- jangan ubah core sebelum cek apakah ada hook/registrar yang sudah tersedia
- treat `app/Cb/Modules` dan `app/Cb/Settings` sebagai extension boundary resmi
- anggap `BaseService`, `WithFormBuilder`, `WithColumnBuilder`, dan `WithAttributeCaller` sebagai jantung framework

