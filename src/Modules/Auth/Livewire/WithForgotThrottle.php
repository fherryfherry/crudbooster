<?php

namespace CrudBooster\Modules\Auth\Livewire;

trait WithForgotThrottle
{
    private $cacheKeyThrottle = 'forgot_throttle_%s';
    private $cacheKeyThrottleBlocked = 'forgot_blocked_%s';
    private $forgotThrottleBlockedMsg = 'Sorry, you have reached the maximum forgot password attempts. Please try again in %s minutes.';

    /**
     * Check throttle blocked
     * @return bool
     */
    private function checkThrottleBlocked(): bool
    {
        $throttle = securitySetting()->getForgotThrottle();
        $duration = securitySetting()->getForgotThrottleBlockedDuration();
        $isBlocked = cache()->get(sprintf($this->cacheKeyThrottleBlocked, request()->ip()));
        if ($throttle > 0 && $isBlocked) {
            // calculate the remaining time, from isBlocked (timestamp) to now
            $remaining = round($duration - abs(now()->diffInMinutes($isBlocked)));
            if($remaining > 0) {
                $message = sprintf($this->forgotThrottleBlockedMsg, $remaining);
                $this->showAlertMessage($message, 'warning');
                return true;
            }
            // remove blocked
            cache()->forget(sprintf($this->cacheKeyThrottleBlocked, request()->ip()));
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
        $throttle = securitySetting()->getForgotThrottle();
        $throttleBlockedDuration = securitySetting()->getForgotThrottleBlockedDuration();
        if ($throttle > 0) {
            $key = sprintf($this->cacheKeyThrottle, request()->ip());
            $current = cache()->get($key) ?? 0;
            cache()->put($key, $current + 1, now()->addMinutes($throttleBlockedDuration));
            $attempts = cache()->get($key);
            if ($attempts > $throttle) {
                cache()->put(sprintf($this->cacheKeyThrottleBlocked, request()->ip()), now(), now()->addMinutes($throttleBlockedDuration));
            }
        }
    }
}
