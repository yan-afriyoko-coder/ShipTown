<div class="help-tip">
    <p>This is the number of orders with a given status</p>
</div>
<div class="card">
    <div class="card-body">
        <h4 class="card-title text-center">LAST 30 DAY ORDERS</h4>
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
                            <td>{{ $status->status_code }}</td>
                            <td class="text-right">{{ $status->order_count }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
