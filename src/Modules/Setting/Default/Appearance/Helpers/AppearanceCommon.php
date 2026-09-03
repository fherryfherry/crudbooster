<?php

namespace CrudBooster\Modules\Setting\Default\Appearance\Helpers;

class AppearanceCommon
{
    private $favicon;
    private $loginLogo;
    private $loginSplash;
    private $loginBackground;
    private $loginCss;
    private $sidebarLogo;
    private $sidebarBackground;
    private $footerText;
    private $welcomeText;
    private $welcomeSubText;

    public function __construct(?array $setting)
    {
        $this->favicon = $setting['favicon'] ?? null;
        $this->loginLogo = $setting['login_logo'] ?? null;
        $this->loginSplash = $setting['login_splash'] ?? null;
        $this->loginBackground = $setting['login_background'] ?? null;
        $this->loginCss = $setting['login_css'] ?? null;
        $this->sidebarLogo = $setting['sidebar_logo'] ?? null;
        $this->sidebarBackground = $setting['sidebar_background'] ?? null;
        $this->footerText = $setting['login_footer_text'] ?? null;
        $this->welcomeText = $setting['login_welcome_text'] ?? null;
        $this->welcomeSubText = $setting['login_welcome_sub_text'] ?? null;
    }

    public function getWelcomeSubText(): mixed
    {
        return $this->welcomeSubText;
    }


    public function getFooterText(): mixed
    {
        return $this->footerText;
    }

    public function getWelcomeText(): mixed
    {
        return $this->welcomeText;
    }


    public function getFavicon(): mixed
    {
        return $this->favicon;
    }


    public function getLoginLogo(): mixed
    {
        return $this->loginLogo;
    }

    public function getLoginSplash(): mixed
    {
        return $this->loginSplash;
    }

    public function getLoginBackground(): mixed
    {
        return $this->loginBackground;
    }

    public function getLoginCss(): mixed
    {
        return $this->loginCss;
    }

    public function getSidebarLogo(): mixed
    {
        return $this->sidebarLogo;
    }

    public function getSidebarBackground(): mixed
    {
        return $this->sidebarBackground;
    }

}
