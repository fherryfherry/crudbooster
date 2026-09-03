<?php

namespace CrudBooster\Modules\Setting\Default\BasicInfo\Helpers;

class BasicInfoProperty
{
    private $app_name;
    private $company_name;
    private $address;
    private $phone;
    private $email;

    public function __construct(?array $setting)
    {
        $this->app_name = $setting['app_name']??'';
        $this->company_name = $setting['company_name']??'';
        $this->address = $setting['address']??'';
        $this->phone = $setting['phone']??'';
        $this->email = $setting['email']??'';
    }

    public function getAppName(): mixed
    {
        return $this->app_name;
    }


    public function getCompanyName(): mixed
    {
        return $this->company_name;
    }

    public function getAddress(): mixed
    {
        return $this->address;
    }

    public function getPhone(): mixed
    {
        return $this->phone;
    }

    public function getEmail(): mixed
    {
        return $this->email;
    }


}
