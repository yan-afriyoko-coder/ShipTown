<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderActionAbstract;

class SetStatusCodeAction extends BaseOrderActionAbstract
{
    /**
     * @param string $options
     */
    public function handle(string $options = '')
    {
        $order = $this->order->refresh();

        if ($order->status_code === $options) {
            return;
        }

        $order->update(['status_code' => $options]);
    }
}
