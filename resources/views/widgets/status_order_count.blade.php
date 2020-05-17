<div class="card text-center">
    <div class="card-body">
        <h4 class="card-title">LAST 30 DAY ORDERS</h5>
        <table>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
            @foreach ($status_order_counts as $status)
                <tr>
                    <td>{{ $status->status_code }}</td>
                    <td>{{ $status->order_count }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
