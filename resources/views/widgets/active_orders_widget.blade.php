<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="status-help-icon-active-orders-widget"></font-awesome-icon>
    <template>
        <tippy to="status-help-icon-active-orders-widget" arrow>
            <p>This is the number of orders with a given status</p>
        </tippy>
    </template>
</div>

<div>
    <h4 class="card-title text-center">ACTIVE ORDERS</h4>
    <div class="row">
        <div class="col-8 offset-2">
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-right">{{ $total_count }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order_status_counts as $order_status)
                    <tr>
                        <td><a href="/orders?sort=order_placed_at&status={{$order_status->status_code }}&sort=order_placed_at" target="_blank">{{$order_status->status_code }}</a></td>
                        <td class="text-right">{{ $order_status->order_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
