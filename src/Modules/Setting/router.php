<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\Setting\Livewire\Setting;

// Setting routes
CBRoute::createRouteOne('setting', Setting::class, ['web', 'auth']);
