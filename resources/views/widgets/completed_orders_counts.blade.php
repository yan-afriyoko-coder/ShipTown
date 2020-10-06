<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="status-help-icon-completed-orders"></font-awesome-icon>
    <template>
        <tippy to="status-help-icon-completed-orders" arrow>
            <p>This is the number of orders with a given status</p>
        </tippy>
    </template>
</div>

<div>
    <h4 class="card-title text-center">COMPLETED ORDERS</h4>
    <h6 class="card-title text-center">LAST 7 DAYS</h6>
    <div class="row">
        <div class="col-8 offset-2">
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-right">Count</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($status_order_counts as $status)
                        <tr>
                            <td><a href="/orders?sort=order_placed_at&status={{$status->status_code }}">{{$status->status_code }}</a></td>
                            <td class="text-right">{{ $status->order_count }}</td>
                        </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
