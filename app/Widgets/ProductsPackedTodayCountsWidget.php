<?php

namespace App\Widgets;

use App\Models\Order;

class ProductsPackedTodayCountsWidget extends AbstractDateSelectorWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count_per_user = Order::query()
            ->select(['packer_user_id','users.name', \DB::raw('sum(total_quantity_ordered) as total')])
            ->whereBetween('packed_at', [
                $this->getStartingDateTime(),
                $this->getEndingDateTime()
            ])
            ->leftJoin('users',
                'packer_user_id', '=', 'users.id')
            ->groupBy(['packer_user_id'])
            ->orderByDesc('total')
            ->get();

        $total_count = 0;

        foreach ($count_per_user as $count) {
            $total_count += $count['total'];
        }

        return view('widgets.products_packed_counts_widget', [
            'config' => $this->config,
            'count_per_user' => $count_per_user,
            'total_count' => $total_count,
        ]);
    }
}
