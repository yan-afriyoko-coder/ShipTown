<?php

namespace App\Listeners\PickQuantityRequiredChangedEvent;

use App\Events\PickQuantityRequiredChangedEvent;
use App\Models\PickRequest;
use App\Services\PicklistService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MovePickRequestToNewPickListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param PickQuantityRequiredChangedEvent $event
     * @return void
     */
    public function handle(PickQuantityRequiredChangedEvent $event)
    {
        $pick = $event->getPick();

        $quantity_to_redistribute = $pick->getOriginal('quantity_required') - $pick->quantity_required;
        $quantity_distributed = 0;

        $newPick = $pick->replicate([
            'quantity_required',
            'picker_user_id',
            'picked_at',
        ]);

        $newPick->quantity_required = $quantity_to_redistribute;

        $newPick->save();

        $pickRequests = PickRequest::query()
            ->where(['pick_id' => $pick->getKey()])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($pickRequests as $pickRequest) {
            $quantity_left_to_distribute = $quantity_to_redistribute - $quantity_distributed;

            if ($pickRequest->quantity_required > $quantity_left_to_distribute) {
                $newPickRequest = $pickRequest->extractToNewPick($quantity_left_to_distribute);
                $newPickRequest->update(['pick_id' => $newPick->getKey()]);
                break;
            }

            $pickRequest->update(['pick_id' => $newPick->getKey()]);

            $quantity_distributed += $pickRequest->quantity_required;
        }
    }
}
