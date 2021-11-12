<?php

namespace App\Modules\Automations\src\Actions;

use App\Modules\Automations\src\BaseOrderAction;
use App\Modules\BoxTop\src\Services\BoxTopService;

class PushToBoxTopOrderAction extends BaseOrderAction
{
    public function handle($options)
    {
        parent::handle($options);

        $response = BoxTopService::postOrder($this->order);

        if (201 === $response->http_response->getStatusCode()) {
            $this->order->status_code = 'complete_twickenham';
            $this->order->save();
            $this->order->log('SureFreight pick created - '. $response->content);
            return;
        }

        $this->order->log('SureFreight pick FAILED - '. $response->content);
    }
}
