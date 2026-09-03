<?php

namespace CrudBooster\Modules\Setting\Default\Security\Helpers;

use Illuminate\Support\Facades\Log;

class SecurityProperty
{
    private array $login_whitelist_ip;
    private int $login_throttle;
    private int $login_throttle_blocked_duration;
    private bool $forgot_status;
    private int $forgot_throttle;
    private int $forgot_throttle_blocked_duration;

    public function __construct(?array $setting)
    {
        $this->login_whitelist_ip = isset($setting['login_whitelist_ip']) ? explode(',', $setting['login_whitelist_ip']) : [];
        $this->login_throttle = $setting['login_throttle'] ?? 0;
        $this->login_throttle_blocked_duration = $setting['login_throttle_blocked_duration'] ?? 0;
        $this->forgot_status = (bool) ($setting['forgot_status'] ?? false);
        $this->forgot_throttle = $setting['forgot_throttle'] ?? 0;
        $this->forgot_throttle_blocked_duration = $setting['forgot_throttle_blocked_duration'] ?? 0;
    }

    public function getLoginWhitelistIp(): array
    {
        return $this->login_whitelist_ip;
    }


    public function getForgotThrottle(): int
    {
        return $this->forgot_throttle;
    }

    public function getForgotThrottleBlockedDuration(): int
    {
        return $this->forgot_throttle_blocked_duration;
    }


    public function getLoginThrottle(): int
    {
        return $this->login_throttle;
    }

    public function getLoginForgotEnabled(): bool
    {
        return $this->forgot_status;
    }

    // get login throttle blocked duration
    public function getLoginThrottleBlockedDuration(): int
    {
        return $this->login_throttle_blocked_duration;
    }


}
