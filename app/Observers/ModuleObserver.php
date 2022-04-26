<?php

namespace App\Observers;

use App\Models\Module;

class ModuleObserver
{
    public function saving(Module $module)
    {
        if ($module->isAttributeChanged('enabled')) {
            if ($module->enabled) {
                return $module->service_provider_class::enabling();
            } else {
                return $module->service_provider_class::disabling();
            }
        }
    }
}
