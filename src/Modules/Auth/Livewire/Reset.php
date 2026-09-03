<?php

namespace CrudBooster\Modules\Auth\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Reset extends Component
{
    use WithAlertMessage;

    public $token;
    public $password;
    public $password_confirmation;
    protected $expires = 24;
    private $email;
    public $isValid;

    public function mount($token): void
    {
        $this->token = $token;
        $this->isValid = (bool) $this->getResetData();

        if(session()->has("message")) {
            $message = session("message");
            $type = session("messageType", "success");
            $position = session("messagePosition", "TOP_RIGHT");
            $this->dispatch('showAlertMessage', $message, $type, $position);
        }
    }

    public function render()
    {
        return view('cb.auth::reset-password')->layout('cb.themes::layout-empty');
    }

    private function getResetData()
    {
        return DB::table('password_reset_tokens')
            ->where('token', $this->token)
            ->where('created_at', '>=', now()->subHours($this->expires))
            ->first();
    }

    public function submit(): void
    {
        $this->validate([
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        if($resetData = $this->getResetData()) {
            DB::table('password_reset_tokens')->where('token', $this->token)->delete();
            $this->showAlertMessage('The password reset link is expired', 'warning');
            return;
        }

        $user = User::query()->where('email', $resetData->email)->first();
        $user->password = Hash::make($this->password);
        $user->save();

        $this->showAlertMessage('Password has been changed successfully', 'success', 2, route('login'));
    }
}
