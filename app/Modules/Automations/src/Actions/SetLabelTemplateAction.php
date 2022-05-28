<?php

namespace App\Modules\Automations\src\Actions;

use App\Modules\Automations\src\Abstracts\BaseOrderActionAbstract;

class SetLabelTemplateAction extends BaseOrderActionAbstract
{
    public function handle($options = '')
    {
        parent::handle($options);

        if ($this->order->label_template !== $options) {
            $this->order->update(['label_template' => $options]) ;
        }
    }
}
