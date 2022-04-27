<?php

namespace App\Observers;

use App\Models\Module;
use Illuminate\Support\Facades\App;

class ModuleObserver
{
    public function saving(Module $module)
    {
        if ($module->isAttributeChanged('enabled')) {
            if ($module->enabled) {
                if ($module->service_provider_class::enabling()) {
//                    App::register(get_called_class())->boot();
                    return true;
                }
            } else {
                return $module->service_provider_class::disabling();
            }
        }
    }
}
