<?php

namespace CrudBooster\Modules\Profile\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Helpers\CbUploader;
use CrudBooster\Modules\User\Services\UserService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use withFileUploads, WithAlertMessage;

    public $user;
    #[Validate('required|string|min:5')]
    public $name;
    #[Validate('required|min:5|email')]
    public $email;
    #[Validate('nullable|image|max:1024')]
    public $photo;
    #[Validate('nullable|string|min:5')]
    public $phone;
    #[Validate('nullable|string|min:5')]
    public $position;
    public $old_password;
    public $password;
    public $password_confirmation;
    public $pageTitle = "Profile";
    public function mount(): void
    {
        $this->user = UserService::findById(auth()->user()->id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->position = $this->user->position;

        if(session()->has("message")) {
            $message = session("message");
            $type = session("messageType", "success");
            $position = session("messagePosition", "TOP_RIGHT");
            $this->dispatch('showAlertMessage', $message, $type, $position);
        }
    }

    public function update(): void
    {
        $this->validate();

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('profile'), navigate: true);
            return;
        }

        $payload = [
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "position" => $this->position,
            "photo" => $this->photo ? CbUploader::uploadFromLivewire($this->photo) : $this->user->photo,
        ];
        UserService::updateWithData($this->user->id, $payload);

        $this->showAlertMessage("Profile updated successfully", "SUCCESS");
    }

    public function changePassword(): void
    {
        $this->validate([
            'old_password'=>'required|current_password',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('profile'), navigate: true);
            return;
        }

        UserService::updateWithData($this->user->id, [
            "password" => $this->password,
        ]);
        $this->showAlertMessage("Password updated successfully", "SUCCESS");
    }

    public function render()
    {
        return view('cb.profile::profile')
            ->layout("cb.themes::layout-app");
    }
}
