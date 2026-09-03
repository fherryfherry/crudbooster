<?php

namespace CrudBooster\Modules\Dashboard\Livewire;

use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Modules\Menu\Services\CBMenuService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Dashboard extends Component
{
    use WithAlertMessage;

    public $labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
    public $dataPoints = [65, 59, 80, 81, 56, 55, 40];

    // data point browser visit for chartJs value only
    public $dataBrowser = [65, 59, 80, 81, 56, 55, 40];
    public $labelBrowser = ['Chrome', 'Firefox', 'IE', 'Safari', 'Edge', 'Others'];

    // data point revenue
    public $dataRevenue = [65, 59, 80, 81, 56, 55, 40];
    public $labelRevenue = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];

    public function mount()
    {
        // Validate if user has access to dashboard
        if(!Gate::check('read','dashboard')) {
            // redirect to first menu
            $menu = CBMenuService::getAllFilteredPermission();
            if($menu->count() > 0) {
                $this->redirect(collect($menu)->first()->menu_url, navigate: true);
                return;
            } else {
                $this->showAlertMessage('You do not have access to this page', 'error');
                $this->redirect(route('logout'), navigate: true);
                return;
            }
        }

        // if menu has a dashboard then it will be redirected to dashboard
        $menu = CBMenuService::getDashboardMenu();
        if ($menu && $menu->menu_url && $menu->menu_value !== 'dashboard') {
            $this->redirect($menu->menu_url, navigate: true);
        }
    }

    public function render()
    {
        return view('cb.modules.dashboard::dashboard')
            ->layout("cb.themes::layout-app");
    }
}
