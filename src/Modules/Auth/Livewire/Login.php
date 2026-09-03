<?php

namespace CrudBooster\Modules\Auth\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Modules\Auth\Events\LoginAttemptFailed;
use CrudBooster\Modules\Auth\Events\LoginAttemptSuccess;
use Illuminate\Support\Facades\Event;
use Livewire\Component;

class Login extends Component
{
    use WithAlertMessage;
    use WithLoginThrottle;

    public $email;
    public $password;
    public $remember;

    public function mount() {
        if(!auth()->guest()) {
            $this->showAlertMessage("Hi, Welcome Back!", 'success');
            return redirect(getCmsPath(config('cb.dashboard_path')));
        }

        if(session()->has("message")) {
            $message = session("message");
            $type = session("messageType", "success");
            $position = session("messagePosition", "TOP_RIGHT");
            $this->dispatch('showAlertMessage', $message, $type, $position);
        }

        $this->email = config('cb.demo_mode') ? config('cb.demo_username') : '';
        $this->password = config('cb.demo_mode') ? config('cb.demo_password') : '';
    }

    public function render()
    {
        return view('cb.auth::login')->layout('cb.themes::layout-empty');
    }

    public function submit()
    {
        $this->validate([
            'email' => 'required|exists:users|email',
            'password' => 'required|min:6'
        ]);

        if($this->checkWhiteListIp()) return false;
        if($this->checkThrottleBlocked()) return false;

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            Event::dispatch(new LoginAttemptSuccess(auth()->user()));
            $this->redirectIntended(getCmsUrl(config('cb.dashboard_path') ?? 'dashboard'), navigate: true);
        } else {
            Event::dispatch(new LoginAttemptFailed($this->email));
            $this->hitThrottle();
            $this->showAlertMessage(__('cb/auth.login_page.login_failed'), 'warning');
        }
    }
}
