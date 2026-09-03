<?php

namespace CrudBooster\Modules\Auth\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoginAttemptFailed
{
    use Dispatchable, SerializesModels;

    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }
}
