<div class="help-tip">
    <p>This is the inline help tip! It can contain all kinds of HTML. Style it as you please.</p>
</div>
<div class="card">
    <div class="card-body">
        <h4 class="card-title text-center">LAST 30 DAY ORDERS</h5>
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
