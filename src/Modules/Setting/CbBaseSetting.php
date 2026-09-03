<?php

namespace CrudBooster\Modules\Setting;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Modules\Setting\Models\CbSetting;
use CrudBooster\Modules\Setting\Services\CbSettingService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class CbBaseSetting extends Component
{
    use WithAlertMessage;
    use WithConfirmMessage;

    public $form = [];
    public $key;

    public function mount()
    {
        if ($this->key) {
            $appEnv = config('app.env');
            // validate only production, staging, development, local
            if (!in_array($appEnv, ['production', 'staging', 'development', 'local'])) {
                throw new \InvalidArgumentException('Invalid app environment');
            }
            $data = CbSetting::where('name', $this->key)->first();
            if($data) {
                if ($appEnv == 'production') {
                    $this->form = $data->production_setting??[];
                } else if ($appEnv == 'staging') {
                    $this->form = $data->staging_setting??[];
                } else if ($appEnv == 'development' || $appEnv == 'local') {
                    $this->form = $data->development_setting??[];
                }
            }
        }
    }

    public function removeFile($name = null)
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $this->redirectIntended(getCmsUrl('/'), navigate: true);
            return;
        }

        // get file path
        $file = $this->form[$name]??null;
        if($file) {
            // remove file
            if(Storage::exists($file)) {
                Storage::delete($file);
            }
            // remove from form
            $this->form[$name] = null;

            // save to database
            CbSettingService::createOrUpdate($this->key, $this->form);
        }
        $this->confirmMessageClose();
    }

    public function save()
    {
        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            return;
        }

        CbSettingService::createOrUpdate($this->key, $this->form);
        $this->showAlertMessage('Setting has been saved', 'success');
    }
}
