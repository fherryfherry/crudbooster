<?php

namespace CrudBooster\Modules\Auth\Livewire;

trait WithLoginThrottle
{
    private $cacheKeyThrottle = 'login_throttle_%s';
    private $cacheKeyThrottleBocked = 'login_blocked_%s';
    private $loginThrottleBlockedMsg = 'Sorry, you have reached the maximum login attempts. Please try again in %s minutes.';
    private $whitelistIpMsg = 'You are not allowed to login.';
    /**
     * Check throttle blocked
     * @return bool
     */
    private function checkThrottleBlocked(): bool
    {
        $throttle = securitySetting()->getLoginThrottle();
        $duration = securitySetting()->getLoginThrottleBlockedDuration();
        $isBlocked = cache()->get(sprintf($this->cacheKeyThrottleBocked, request()->ip()));
        if ($throttle > 0 && $isBlocked) {
            // calculate the remaining time, from isBlocked (timestamp) to now
            $remaining = round($duration - abs(now()->diffInMinutes($isBlocked)));
            if($remaining > 0) {
                $message = sprintf($this->loginThrottleBlockedMsg, $remaining);
                $this->showAlertMessage($message, 'warning');
                return true;
            }
            // remove blocked
            cache()->forget(sprintf($this->cacheKeyThrottleBocked, request()->ip()));
            cache()->forget(sprintf($this->cacheKeyThrottle, request()->ip()));
        }
        return false;
    }

    /**
     * Hit throttle
     * @return void
     */
    private function hitThrottle(): void
    {
        $throttle = securitySetting()->getLoginThrottle();
        $throttleBlockedDuration = securitySetting()->getLoginThrottleBlockedDuration();
        if ($throttle > 0) {
            $key = sprintf($this->cacheKeyThrottle, request()->ip());
            $current = cache()->get($key) ?? 0;
            cache()->put($key, $current + 1, now()->addMinutes($throttleBlockedDuration));
            $attempts = cache()->get($key);
            if ($attempts > $throttle) {
                cache()->put(sprintf($this->cacheKeyThrottleBocked, request()->ip()), now(), now()->addMinutes($throttleBlockedDuration));
            }
        }
    }

    private function checkWhiteListIp()
    {
        $whiteListIp = securitySetting()->getLoginWhitelistIp();
        if($whiteListIp && count($whiteListIp)>0) {
            $ip = request()->ip();
            if(!in_array($ip, $whiteListIp)) {
                $this->showAlertMessage($this->whitelistIpMsg, 'warning');
                return true;
            }
        }
        return false;
    }

}
