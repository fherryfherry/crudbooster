<?php

namespace CrudBooster\Modules\Auth\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class Forgot extends Component
{
    use WithAlertMessage;
    use WithForgotThrottle;

    public $email;

    public function mount()
    {
        if(!securitySetting()->getLoginForgotEnabled()) $this->redirect(getCmsUrl('auth/login'));
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
    }

    public function render()
    {
        return view('cb.auth::forgot')->layout('cb.themes::layout-empty');
    }

    public function submit(): void
    {
        if($this->checkThrottleBlocked()) return;

        $this->validate([
            'email' => 'required|email|exists:users,email|not_in:password_reset_tokens,email'
        ]);

        if(DB::table('password_reset_tokens')->where('email', $this->email)->exists()) {
            $this->showAlertMessage('Password reset email already sent. Please check your mailbox.', 'warning');
            $this->hitThrottle();
            return;
        }

        // Send email confirmation to reset password
        $user = User::query()->where('email', $this->email)->first();
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $this->email,
            'token' => $token,
            'created_at' => now()
        ]);
        Mail::send('cb.auth::email.forgot', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Reset Password');
        });
        $this->showAlertMessage('Password reset email sent successfully!. Please open your mailbox to check next step.', 'success');
        $this->hitThrottle();
    }
}
