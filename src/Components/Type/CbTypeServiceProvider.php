<?php

namespace CrudBooster\Components\Type;

use Illuminate\Support\ServiceProvider;

class CbTypeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(Radio\RadioServiceProvider::class);
        $this->app->register(Checkbox\CheckboxServiceProvider::class);
        $this->app->register(Date\DateServiceProvider::class);
        $this->app->register(DateTime\DateTimeServiceProvider::class);
        $this->app->register(Time\TimeServiceProvider::class);
        $this->app->register(Text\TextServiceProvider::class);
        $this->app->register(TextArea\TextAreaServiceProvider::class);
        $this->app->register(Email\EmailServiceProvider::class);
        $this->app->register(Money\MoneyServiceProvider::class);
        $this->app->register(Password\PasswordServiceProvider::class);
        $this->app->register(JsonChecklist\JsonChecklistServiceProvider::class);
        $this->app->register(JsonTable\JsonTableServiceProvider::class);
        $this->app->register(SelectChips\SelectChipServiceProvider::class);
        $this->app->register(Select\SelectServiceProvider::class);
        $this->app->register(Number\NumberServiceProvider::class);
        $this->app->register(Url\UrlServiceProvider::class);
        $this->app->register(Image\ImageServiceProvider::class);
        $this->app->register(Trix\TrixServiceProvider::class);
        $this->app->register(TinyMce\TinyMceServiceProvider::class);
        $this->app->register(File\FileServiceProvider::class);
        $this->app->register(Summernote\SummernoteServiceProvider::class);
        $this->app->register(EmptyField\EmptyFieldServiceProvider::class);
        require_once __DIR__ . '/Common.php';
    }
}
