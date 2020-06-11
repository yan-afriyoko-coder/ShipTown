<?php

namespace App\Jobs\Api2cart;

use App\Models\Api2cartOrderImports;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductOption;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class ProcessImportedOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var bool
     */
    public $finishedSuccessfully;

    private $orderImport = null;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(Api2cartOrderImports $orderImport)
    {
        $this->finishedSuccessfully = false;
        info('Job Api2cart\ProcessImportedOrder dispatched');
        $this->orderImport = $orderImport;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $attributes = $this->getAttributes($this->orderImport['raw_import']);

        $order = Order::updateOrCreate(
            [
                "order_number" => $this->orderImport['raw_import']['id'],
            ],
            $attributes
        );

        $order->orderProducts()->delete();

        foreach ($this->orderImport['raw_import']['order_products'] as $rawOrderProduct) {
            $orderProductData = Collection::make($rawOrderProduct);
            $orderProduct = new OrderProduct();
            $orderProduct->fill($orderProductData->except('options')->toArray());

            $order->orderProducts()->save($orderProduct);

            $orderProductOptionMap = array_map(function ($o) {
                return new OrderProductOption($o);
            }, $orderProductData['options']);

            $orderProduct->options()->saveMany($orderProductOptionMap);
        }

        $this->orderImport->when_processed = now();
        $this->orderImport->save();

        // finalize
        $this->finishedSuccessfully = true;
    }

    /**
     * @param array $order
     * @return Collection
     */
    public function getChronologicalStatusHistory(array $order){
        return Collection::make($order['status']['history'])
            ->sort(function ($a, $b) {
                $a_time = Carbon::make($a['modified_time']['value']);
                $b_time = Carbon::make($b['modified_time']['value']);
                return $a_time > $b_time;
            });
    }

    /**
     * @param $order
     * @return mixed
     */
    private function getAttributes($order)
    {
        $result = [];
        $result['order_number'] = $order['id'];

        $result['order_placed_at'] = Carbon::createFromFormat(
            $order['create_at']['format'],
            $order['create_at']['value']
        );

        $result['status_code'] = $order['status']['id'];

        $statuses = $this->getChronologicalStatusHistory($order);

        foreach ($statuses as $status) {
            if ($status['id'] !== 'processing') {

                $time = $status['modified_time'];

                if (!is_null($time['value'])) {
                    $result['order_closed_at'] = Carbon::createFromFormat($time['format'], $time['value']);
                    break;
                }

            }
        }


        $result['products_count'] = 0;

        foreach ($order['order_products'] as $product) {
            $result['products_count'] += $product['quantity'];
        }

        return $result;
    }
}
