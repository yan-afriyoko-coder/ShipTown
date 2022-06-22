<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Resources\InventoryMovementResource;
use App\Models\InventoryMovement;
use App\Modules\Webhooks\src\Models\PendingWebhook;
use App\Modules\Webhooks\src\Services\SnsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishInventoryMovementWebhooksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $query = PendingWebhook::query()
            ->where([
                'model_class' => InventoryMovement::class,
                'reserved_at' => null
            ])
            ->limit(10);

        $chunk = $query->get();

        while ($chunk->isNotEmpty()) {
            $pendingWebhookIds = $chunk->pluck('id');

            try {
                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->update(['reserved_at' => now()]);

                $this->publishInventoryMovementMessage($chunk);

                PendingWebhook::query()->whereIn('id', $pendingWebhookIds)->delete();
            } catch (Exception $exception) {
                PendingWebhook::query()
                    ->whereIn('id', $pendingWebhookIds)
                    ->update(['reserved_at' => null]);

                throw $exception;
            }

            $chunk = $query->get();
        }
    }

    /**
     * @param $chunk
     */
    private function publishInventoryMovementMessage($chunk): void
    {
        $inventoryMovementsCollection = InventoryMovementResource::collection(
            InventoryMovement::query()
                ->whereIn('id', $chunk->pluck('model_id'))
                ->orderBy('id')
                ->with(['product', 'warehouse', 'user'])
                ->get()
        );

        $payload = collect(['InventoryMovements' => $inventoryMovementsCollection]);

        SnsService::publishNew($payload->toJson());
    }
}
